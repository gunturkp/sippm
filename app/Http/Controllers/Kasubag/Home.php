<?php

namespace App\Http\Controllers\Kasubag;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Pengajuan;
use App\Teknisi;
use App\PelaksanaPengajuan;
use App\MasterSukuCadang;
use App\ListSukuCadangPengajuan;
use App\JadwalPemeliharaan;
use App\MasterInventory;
use App\MasterRuangan;
use App\Pemeliharaan;
use App\ListSukuCadangPemeliharaan;
use App\User;
use App\Mail;

class Home extends Controller
{
    public function __construct()
    {
        $this->middleware('kasubag');
    }

    public function index(){
        $pengajuan = Pengajuan::all();
        $teknisi = Teknisi::all();
        $sukucadang = MasterSukuCadang::all();
        $listsukucadang = ListSukuCadangPengajuan::all();
        return view('kasubag/index',compact('pengajuan','teknisi','sukucadang','listsukucadang'));
    }

    public function Pemeliharaan(){
        $jadwalpemeliharaan = JadwalPemeliharaan::all();
        $listsukucadang = ListSukuCadangPemeliharaan::all();
        $teknisi = Teknisi::all();
        $inventory = MasterInventory::all();
        $ruangan = MasterRuangan::all();
        $pemeliharaan = Pemeliharaan::all();
        return view('kasubag/pemeliharaan',compact('jadwalpemeliharaan','teknisi','inventory','listsukucadang','pemeliharaan', 'ruangan'));
    }
    public function Kalibrasi(){
        $jadwalpemeliharaan = JadwalPemeliharaan::where('is_year', '=', 1)->get();
        $listsukucadang = ListSukuCadangPemeliharaan::all();
        $teknisi = Teknisi::all();
        $inventory = MasterInventory::all();
        $ruangan = MasterRuangan::all();
        $pemeliharaan = Pemeliharaan::all();
        return view('kasubag/kalibrasi',compact('jadwalpemeliharaan','teknisi','inventory','listsukucadang','pemeliharaan', 'ruangan'));
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
        return view('kasubag/pesan',compact('user','id_user','notread'));
    }

    public function SubmitPersetujuanPengajuan(Request $request){
        if($request->input('submit') == 1){
            Pengajuan::where('id_pengajuan', $request->input('id_pengajuan'))
                ->update(['step' => 1]);
            if($request->input('teknisi_1') != null){
                $pelaksanapengajuan = new PelaksanaPengajuan;
                $pelaksanapengajuan->id_teknisi = $request->input('teknisi_1');
                $pelaksanapengajuan->id_pengajuan = $request->input('id_pengajuan');
                $pelaksanapengajuan->save();
            }
            if($request->input('teknisi_2') != null){
                $pelaksanapengajuan = new PelaksanaPengajuan;
                $pelaksanapengajuan->id_teknisi = $request->input('teknisi_2');
                $pelaksanapengajuan->id_pengajuan = $request->input('id_pengajuan');
                $pelaksanapengajuan->save();
            }
            if($request->input('teknisi_3') != null){
                $pelaksanapengajuan = new PelaksanaPengajuan;
                $pelaksanapengajuan->id_teknisi = $request->input('teknisi_3');
                $pelaksanapengajuan->id_pengajuan = $request->input('id_pengajuan');
                $pelaksanapengajuan->save();
            }
        }else{
            Pengajuan::where('id_pengajuan', $request->input('id_pengajuan'))
                ->update(['step' => 6]);
        }
        return back();
    }

    public function SubmitValidasiSukuCadang(Request $request){
        if($request->input('submit') == 1){
            ListSukuCadangPengajuan::where('id', $request->input('id'))
                ->update([
                    'is_approve' => 1,
                    'jumlah' => $request->input('jumlah_sukucadang')
                ]);
            $sukucadang = MasterSukuCadang::find($request->input('id_suku_cadang'));
            $jumlahnow = $sukucadang->jumlah - $request->input('jumlah_sukucadang');
            $sukucadang->jumlah = $jumlahnow;
            $sukucadang->save();
        }else{
            ListSukuCadangPengajuan::where('id', $request->input('id'))
                ->update(['is_approve' => 2]);
        }
        return back();
    }

    public function SubmitSukuCadangPemeliharaan(Request $request){
        if($request->input('submit') == 1){
            ListSukuCadangPemeliharaan::where('id', $request->input('id'))
                ->update([
                    'is_approve' => 1,
                    'jumlah' => $request->input('jumlah_sukucadang')
                ]);
            $sukucadang = MasterSukuCadang::find($request->input('id_suku_cadang'));
            $jumlahnow = $sukucadang->jumlah - $request->input('jumlah_sukucadang');
            $sukucadang->jumlah = $jumlahnow;
            $sukucadang->save();
        }else{
            ListSukuCadangPemeliharaan::where('id', $request->input('id'))
                ->update(['is_approve' => 2]);
        }
        return back();
    }

    public function SubmitTambahPemeliharaan(Request $request){
        if($request->input('act') == "edit"){
            $pemeliharaan = JadwalPemeliharaan::find($request->input('id_jadwal_pemeliharaan'));
            $pemeliharaan->id_inventory = $request->input('id_inventory');
            $pemeliharaan->id_teknisi = $request->input('id_teknisi');
            $pemeliharaan->hari = $request->input('hari');
            $pemeliharaan->is_year = $request->input('is_year');
            $pemeliharaan->waktu = $request->input('waktu');
        }else{
            $pemeliharaan = new JadwalPemeliharaan;
            $pemeliharaan->id_inventory = $request->input('id_inventory');
            $pemeliharaan->id_teknisi = $request->input('id_teknisi');
            $pemeliharaan->hari = $request->input('hari');
            $pemeliharaan->is_year = $request->input('is_year');
            $pemeliharaan->waktu = $request->input('waktu');
        }
        $pemeliharaan->save();

        return back();
    }

    public function SubmitHapusJadwalPemeliharaan($id_jadwal_pemeliharaan){
        JadwalPemeliharaan::where('id_jadwal_pemeliharaan',$id_jadwal_pemeliharaan)->delete();

        return back();
    }
}
