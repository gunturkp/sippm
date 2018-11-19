<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Pengajuan;
use App\MasterTipeInventory;
use App\MasterInventory;
use App\MasterTipeRuangan;
use App\MasterRuangan;
use App\MasterSukuCadang;
use App\ListSukuCadangPemeliharaan;
use App\ListSukuCadangPengajuan;
use App\PelaksanaPengajuan;
use App\JadwalPemeliharaan;
use App\Pemeliharaan;
use App\User;
use App\Teknisi;
use Excel;
use Hash;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Exports\UsersExport;
use App\Exports\ExportPemeliharaan;
use App\Exports\ExportInventory;
use App\Mail;
use DB;

class Home extends Controller
{
    use Exportable;
    public function __construct()
    {
        $this->middleware('admin');
    }
    
    public function index(){
        $pengajuan = Pengajuan::all();
        $listsukucadang = ListSukuCadangPengajuan::all();
        return view('admin/index',compact('pengajuan','listsukucadang'));
    }

    public function Dashboard(){
        $year = date("Y");
        $month = date("m");
        $pengajuan[1] = Pengajuan::all()->count(); //all
        $pengajuan[2] = Pengajuan::where('step','=',5)->whereYear('created_at','=',$year)->count(); //selesai
        $pengajuan[3] = $pengajuan[1] - $pengajuan[2]; //berjalan

        $respon_time[1] = 0; // < 15 menit
        $respon_time[2] = 0; // = 15 menit
        $respon_time[3] = 0; // > 15 menit

        $done_time[1] = 0; // < 60 menit
        $done_time[2] = 0; // = 60 menit
        $done_time[3] = 0; // > 60 menit

        // https://stackoverflow.com/questions/365191/how-to-get-time-difference-in-minutes-in-php
        //Laporan Respon & Downtime keseluruhan
        foreach(Pengajuan::all() as $pp){
            if($pp->respon_time != null){
                $start_date = new \DateTime($pp->created_at);
                $diff = $start_date->diff(new \DateTime($pp->respon_time));
                switch (true){
                    case $diff->i < 15:
                        $respon_time[1]++;
                        break;
                    case $diff->i == 15:
                        $respon_time[2]++;
                        break;
                    case $diff->i > 15:
                        $respon_time[3]++;
                        break;
                }
                if($pp->done_time != null){
                    $start_date2 = new \DateTime($pp->created_at);
                    $diff2 = $start_date->diff(new \DateTime($pp->done_time));
                    switch (true){
                        case $diff2->i < 60:
                            $done_time[1]++;
                            break;
                        case $diff2->i == 60:
                            $done_time[2]++;
                            break;
                        case $diff2->i > 60:
                            $done_time[3]++;
                            break;
                    }
                }
            }
        }

        //Laporan Respon & Downtime Per Bulan
        for($j = 1;$j<=12;$j++){
            $responbulan[$j] = 0;
            $bulan[$j] = date('F', mktime(0, 0, 0, $j, 10));
            foreach(Pengajuan::whereYear('created_at','=',$year)->whereMonth('created_at','=',$j)->get() as $pp){
                if($pp->respon_time != null){
                    $start_date = new \DateTime($pp->created_at);
                    $diff = $start_date->diff(new \DateTime($pp->respon_time));
                    switch (true){
                        case $diff->i < 15:
                            $responbulan[$j]++;
                            break;
                    }
                }
            }
        }
        
        //dd($responbulan[1]);
        //Laporan Tiap Ruang
        $jumlahruang = MasterRuangan::count();
        for($i = 1; $i<=$jumlahruang;$i++){
            $hitungruang[$i] = Pengajuan::whereYear('created_at','=',$year)->whereMonth('created_at','=',$month)->with('inventory')->whereHas('inventory', function($q) use ($i){$q->with('ruangan')->where('id_ruangan', '=', $i);})->count();
            $ruang[$i] = MasterRuangan::where('id_ruangan', '=', $i)->pluck('nama_ruangan');
        }
        $status[1] = MasterInventory::where('status', '=',0)->count(); //Baik
        $status[2] = MasterInventory::where('status', '=',1)->count(); //Tidak Baik

        $pemeliharaan[1] = JadwalPemeliharaan::where('is_year', '=',0)->count();//all preventive
        $pemeliharaan[2] = JadwalPemeliharaan::where('is_year', '=',0)->whereYear('created_at','=',$year)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();//done
        $pemeliharaan[3] = $pemeliharaan[1] - $pemeliharaan[2]; //on going

        $kalibrasi[1] = JadwalPemeliharaan::where('is_year', '=',1)->count();//all preventive
        $kalibrasi[2] = JadwalPemeliharaan::where('is_year', '=',1)->whereYear('created_at','=',$year)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();//done
        $kalibrasi[3] = $kalibrasi[1] - $kalibrasi[2]; //on going

        $hitungkalibrasi[1] = JadwalPemeliharaan::where('is_year', '=',1)->whereYear('created_at','=',$year)->whereMonth('created_at','=',1)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungkalibrasi[2] = JadwalPemeliharaan::where('is_year', '=',1)->whereYear('created_at','=',$year)->whereMonth('created_at','=',2)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungkalibrasi[3] = JadwalPemeliharaan::where('is_year', '=',1)->whereYear('created_at','=',$year)->whereMonth('created_at','=',3)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungkalibrasi[4] = JadwalPemeliharaan::where('is_year', '=',1)->whereYear('created_at','=',$year)->whereMonth('created_at','=',4)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungkalibrasi[5] = JadwalPemeliharaan::where('is_year', '=',1)->whereYear('created_at','=',$year)->whereMonth('created_at','=',5)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungkalibrasi[6] = JadwalPemeliharaan::where('is_year', '=',1)->whereYear('created_at','=',$year)->whereMonth('created_at','=',6)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungkalibrasi[7] = JadwalPemeliharaan::where('is_year', '=',1)->whereYear('created_at','=',$year)->whereMonth('created_at','=',7)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungkalibrasi[8] = JadwalPemeliharaan::where('is_year', '=',1)->whereYear('created_at','=',$year)->whereMonth('created_at','=',8)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungkalibrasi[9] = JadwalPemeliharaan::where('is_year', '=',1)->whereYear('created_at','=',$year)->whereMonth('created_at','=',9)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungkalibrasi[10] = JadwalPemeliharaan::where('is_year', '=',1)->whereYear('created_at','=',$year)->whereMonth('created_at','=',10)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungkalibrasi[11] = JadwalPemeliharaan::where('is_year', '=',1)->whereYear('created_at','=',$year)->whereMonth('created_at','=',11)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungkalibrasi[12] = JadwalPemeliharaan::where('is_year', '=',1)->whereYear('created_at','=',$year)->whereMonth('created_at','=',12)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();

        $hitungpreventive[1] = JadwalPemeliharaan::where('is_year', '=',0)->whereYear('created_at','=',$year)->whereMonth('created_at','=',1)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungpreventive[2] = JadwalPemeliharaan::where('is_year', '=',0)->whereYear('created_at','=',$year)->whereMonth('created_at','=',2)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungpreventive[3] = JadwalPemeliharaan::where('is_year', '=',0)->whereYear('created_at','=',$year)->whereMonth('created_at','=',3)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungpreventive[4] = JadwalPemeliharaan::where('is_year', '=',0)->whereYear('created_at','=',$year)->whereMonth('created_at','=',4)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungpreventive[5] = JadwalPemeliharaan::where('is_year', '=',0)->whereYear('created_at','=',$year)->whereMonth('created_at','=',5)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungpreventive[6] = JadwalPemeliharaan::where('is_year', '=',0)->whereYear('created_at','=',$year)->whereMonth('created_at','=',6)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungpreventive[7] = JadwalPemeliharaan::where('is_year', '=',0)->whereYear('created_at','=',$year)->whereMonth('created_at','=',7)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungpreventive[8] = JadwalPemeliharaan::where('is_year', '=',0)->whereYear('created_at','=',$year)->whereMonth('created_at','=',8)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungpreventive[9] = JadwalPemeliharaan::where('is_year', '=',0)->whereYear('created_at','=',$year)->whereMonth('created_at','=',9)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungpreventive[10] = JadwalPemeliharaan::where('is_year', '=',0)->whereYear('created_at','=',$year)->whereMonth('created_at','=',10)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungpreventive[11] = JadwalPemeliharaan::where('is_year', '=',0)->whereYear('created_at','=',$year)->whereMonth('created_at','=',11)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();
            $hitungpreventive[12] = JadwalPemeliharaan::where('is_year', '=',0)->whereYear('created_at','=',$year)->whereMonth('created_at','=',12)->with('pemeliharaan')->whereHas('pemeliharaan', function($q){$q->where('step', '=',1);})->count();

        $hitungpengajuan[1] = Pengajuan::whereYear('created_at','=',$year)->whereMonth('created_at','=',1)->count();
            $hitungpengajuan[2] = Pengajuan::whereYear('created_at','=',$year)->whereMonth('created_at','=',2)->count();
            $hitungpengajuan[3] = Pengajuan::whereYear('created_at','=',$year)->whereMonth('created_at','=',3)->count();
            $hitungpengajuan[4] = Pengajuan::whereYear('created_at','=',$year)->whereMonth('created_at','=',4)->count();
            $hitungpengajuan[5] = Pengajuan::whereYear('created_at','=',$year)->whereMonth('created_at','=',5)->count();
            $hitungpengajuan[6] = Pengajuan::whereYear('created_at','=',$year)->whereMonth('created_at','=',6)->count();
            $hitungpengajuan[7] = Pengajuan::whereYear('created_at','=',$year)->whereMonth('created_at','=',7)->count();
            $hitungpengajuan[8] = Pengajuan::whereYear('created_at','=',$year)->whereMonth('created_at','=',8)->count();
            $hitungpengajuan[9] = Pengajuan::whereYear('created_at','=',$year)->whereMonth('created_at','=',9)->count();
            $hitungpengajuan[10] = Pengajuan::whereYear('created_at','=',$year)->whereMonth('created_at','=',10)->count();
            $hitungpengajuan[11] = Pengajuan::whereYear('created_at','=',$year)->whereMonth('created_at','=',11)->count();
            $hitungpengajuan[12] = Pengajuan::whereYear('created_at','=',$year)->whereMonth('created_at','=',12)->count();
            $hitungpengajuan[13] = $hitungpengajuan[1]+$hitungpengajuan[2]+$hitungpengajuan[3];
            $hitungpengajuan[14] = $hitungpengajuan[4]+$hitungpengajuan[5]+$hitungpengajuan[6];
            $hitungpengajuan[15] = $hitungpengajuan[7]+$hitungpengajuan[8]+$hitungpengajuan[9];
            $hitungpengajuan[16] = $hitungpengajuan[10]+$hitungpengajuan[11]+$hitungpengajuan[12];
        //dd($hitungpengajuan[16]);

        $cyto[1] = Pengajuan::whereYear('created_at','=',$year)->whereMonth('created_at','=',$month)->where('cyto','=',0)->count();//Tidak
        $cyto[2] = Pengajuan::whereYear('created_at','=',$year)->whereMonth('created_at','=',$month)->where('cyto','=',1)->count();//Iya
        return view('admin/dashboard',compact('pengajuan','respon_time','done_time', 'pemeliharaan', 'kalibrasi','status', 'hitungpengajuan','cyto', 'jumlahruang', 'hitungruang', 'ruang', 'responbulan', 'bulan'));
    }

    public function export()
    {
        return (new UsersExport)->download('laporan_pengajuan_perbaikan.xlsx');
    }

    public function exportpemeliharaan()
    {
        return (new ExportPemeliharaan)->download('laporan_pemeliharaan.xlsx');
    }

    public function exportinventory()
    {
        return (new ExportInventory)->download('laporan_inventory.xlsx');
    }

    public function Pemeliharaan(){
        $jadwalpemeliharaan = JadwalPemeliharaan::where('is_year', '=', 0)->get();
        return view('admin/pemeliharaan',compact('jadwalpemeliharaan'));
    }

    public function Kalibrasi(){
        $jadwalpemeliharaan = JadwalPemeliharaan::where('is_year', '=', 1)->get();
        return view('admin/kalibrasi',compact('jadwalpemeliharaan'));
    }

    public function MasterInventory(){
        $tipeinventory = MasterTipeInventory::all();
        $ruangan = MasterRuangan::all();
        $inventory = MasterInventory::all();
        return view('admin/inventory', compact('tipeinventory','ruangan','inventory'));
    }

    public function MasterRuangan(){
        $tiperuangan = MasterTipeRuangan::all();
        $ruangan = MasterRuangan::all();
        return view('admin/ruangan', compact('tiperuangan','ruangan'));
        // return $ruangan->first()->tiperuangan;
    }

    public function MasterSukuCadang(){
        $sukucadang = MasterSukuCadang::all();
        return view('admin/sukucadang', compact('sukucadang'));
    }

    public function MasterAkun(){
        $teknisi = User::where('role',3)->get();
        $user = User::where('role',0)->get();
        $ruangan = MasterTipeRuangan::all();
        return view('admin/akun', compact('teknisi','user','ruangan'));
    }

    public function Message(){
        $notread = [];
        $messagenotread = Mail::where('user_id_receiver','=',\Auth::user()->id)
            ->where('is_read',0)
            ->get();
        
        foreach($messagenotread as $msg){
            if(!isset($notread[$msg->user_id_sender])){
                $notread[$msg->user_id_sender] = 1;
            }
        }

        $user = User::orderBy('role', 'desc')->get();
        $id_user = \Auth::user()->id;
        return view('admin/pesan',compact('user','id_user','notread'));
    }

    public function SubmitTipeInventory(Request $request){
        if($request->input('act') == "edit"){
            $tipeinventory = MasterTipeInventory::find($request->id_tipe_inventory);
            $tipeinventory->nama_tipe_inventory = $request->input('nama_tipe_inventory');
        }else{
            $tipeinventory = new MasterTipeInventory;
            $tipeinventory->nama_tipe_inventory = $request->input('nama_tipe_inventory');
        }
        $tipeinventory->save();

        return back();
    }

    public function SubmitInventory(Request $request){
        if($request->input('act') == "edit"){
            $inventory = MasterInventory::find($request->id_inventory);
            $inventory->id_ruangan = $request->input('id_ruangan');
            $inventory->id_tipe_inventory = $request->input('id_tipe_inventory');
            $inventory->kode_alat = $request->input('kode_alat');
            $inventory->nama_alat = $request->input('nama_alat');
            $inventory->merk_barang = $request->input('merk_barang');
            $inventory->spesifikasi = $request->input('spesifikasi');
            $inventory->serial_number = $request->input('serial_number');
        }else{
            $inventory = new MasterInventory;
            $inventory->id_ruangan = $request->input('id_ruangan');
            $inventory->id_tipe_inventory = $request->input('id_tipe_inventory');
            $inventory->kode_alat = $request->input('kode_alat');
            $inventory->nama_alat = $request->input('nama_alat');
            $inventory->merk_barang = $request->input('merk_barang');
            $inventory->spesifikasi = $request->input('spesifikasi');
            $inventory->serial_number = $request->input('serial_number');
        }
        $inventory->save();

        $image = $request->file('img_inventory');
        
        if($image != null){
            $image->move(public_path('/img_inventory'), ($request->id_inventory.".png"));
        }

        return back();
    }

    public function SubmitTipeRuangan(Request $request){
        if($request->input('act') == "edit"){
            $ruangan = MasterTipeRuangan::find($request->id_tipe_ruangan);
            $ruangan->nama_tipe_ruangan = $request->input('nama_tipe_ruangan');
        }else{
            $ruangan = new MasterTipeRuangan;
            $ruangan->nama_tipe_ruangan = $request->input('nama_tipe_ruangan');
        }
        $ruangan->save();

        return back();
    }

    public function SubmitRuangan(Request $request){
        if($request->input('act') == "edit"){
            $ruangan = MasterRuangan::find($request->id_ruangan);
            $ruangan->id_tipe_ruangan = $request->input('id_tipe_ruangan');
            $ruangan->nama_ruangan = $request->input('nama_ruangan');
            $ruangan->jarak = $request->input('jarak');
        }else{
            $ruangan = new MasterRuangan;
            $ruangan->id_tipe_ruangan = $request->input('id_tipe_ruangan');
            $ruangan->nama_ruangan = $request->input('nama_ruangan');
            $ruangan->jarak = $request->input('jarak');
        }
        $ruangan->save();

        return back();
    }

    public function SubmitSukuCadang(Request $request){
        if($request->input('act') == "edit"){
            $sukucadang = MasterSukuCadang::find($request->id_suku_cadang);
            $sukucadang->nama_suku_cadang = $request->input('nama_suku_cadang');
            $sukucadang->jumlah = $request->input('jumlah');
        }else{
            $sukucadang = new MasterSukuCadang;
            $sukucadang->nama_suku_cadang = $request->input('nama_suku_cadang');
            $sukucadang->jumlah = $request->input('jumlah');
        }
        $sukucadang->save();

        return back();
    }

    public function SubmitUser(Request $request){
        if($request->input('act') == "edit"){
            $user = User::find($request->user_id);
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->id_tipe_ruangan = $request->input('id_tipe_ruangan');
        }else{
            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->id_tipe_ruangan = $request->input('id_tipe_ruangan');
            $user->role = 0;
        }
        $user->save();

        return back();
    }

    public function SubmitTeknisi(Request $request){
        if($request->input('act') == "edit"){
            $user = User::find($request->user_id);
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->save();
        }else{
            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->role = 3;

            $user->save();

            $user_id = $user->id;
            $teknisi = new Teknisi;
            $teknisi->user_id = $user_id;
            $teknisi->save();
        }

        return back();
    }

}
