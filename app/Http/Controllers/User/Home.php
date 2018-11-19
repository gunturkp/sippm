<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Pengajuan;
use App\MasterInventory;
use App\MasterTipeInventory;
use App\MasterRuangan;
use App\JadwalPemeliharaan;
use App\User;
use App\Mail;
use DB;

class Home extends Controller
{
    public function __construct()
    {
        $this->middleware('user');
    }

    public function index(){
        $pengajuan = Pengajuan::where('user_id', \Auth::user()->id)->get();
        $ruangan = MasterRuangan::all();
        return view('user/index',compact('pengajuan','ruangan'));
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
        return view('user/pesan',compact('user','id_user','notread'));
    }

    public function MasterInventory(){
        $tipeinventory = MasterTipeInventory::all();
        $ruangan = MasterRuangan::where('id_tipe_ruangan', '=', \Auth::user()->id_tipe_ruangan)->get();
        $inventory = DB::table('master_inventory')
            ->join('master_ruangan', 'master_inventory.id_ruangan', '=', 'master_ruangan.id_ruangan')
            ->join('master_tipe_inventory', 'master_inventory.id_tipe_inventory', '=', 'master_tipe_inventory.id_tipe_inventory')
            ->join('master_tipe_ruangan', 'master_ruangan.id_tipe_ruangan', '=', 'master_tipe_ruangan.id_tipe_ruangan')
            ->join('users', 'master_ruangan.id_tipe_ruangan', '=', 'users.id_tipe_ruangan')
            ->select('*')
            ->where('users.id', '=', \Auth::user()->id)
            ->get();
        return view('user/inventory', compact('tipeinventory','ruangan','inventory'));
    }

    public function Pemeliharaan(){
        $jadwalpemeliharaan = JadwalPemeliharaan::with('inventory')->whereHas('inventory', function($q){$q->with('ruangan')->whereHas('ruangan', function($r){$r->with('tiperuangan')->whereHas('tiperuangan', function($s){$s->where('id_tipe_ruangan', '=', \Auth::user()->id_tipe_ruangan);});});})->where('is_year', '=', 0)->get();
        return view('user/pemeliharaan',compact('jadwalpemeliharaan'));
    }

    public function Kalibrasi(){
        $jadwalpemeliharaan = JadwalPemeliharaan::with('inventory')->whereHas('inventory', function($q){$q->with('ruangan')->whereHas('ruangan', function($r){$r->with('tiperuangan')->whereHas('tiperuangan', function($s){$s->where('id_tipe_ruangan', '=', \Auth::user()->id_tipe_ruangan);});});})->where('is_year', '=', 1)->get();
        return view('user/kalibrasi',compact('jadwalpemeliharaan'));
    }

    public function SubmitTambahPengajuan(Request $request){
        $pengajuan = new Pengajuan;
        $pengajuan->user_id = \Auth::user()->id;
        $pengajuan->id_inventory = $request->input('id_inventory');
        $pengajuan->keluhan = $request->input('keluhan');
        $pengajuan->cyto = $request->input('cyto');
        $pengajuan->step = 0;
        $pengajuan->save();

        return back();
    }
    
    public function SubmitPenyelesaianPerbaikan(Request $request){
        if($request->input('button') == 1){
            $data = [
                'step' => 5,
                'done_time' => Carbon::now()
            ];
        }else{
            $data = [
                'step' => 3
            ];
        }

        Pengajuan::where('id_pengajuan', $request->input('id_pengajuan'))
            ->update($data);
        return back();
    }

    public function submitKonfirmasiTeknisiDatang(Request $request){
        Pengajuan::where('id_pengajuan', $request->input('id_pengajuan'))
            ->update([
                'step' => 2,
                'respon_time' => Carbon::now()
            ]);
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
}
