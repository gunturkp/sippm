<?php

namespace App\Http\Controllers\Teknisi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Pengajuan;
use App\Teknisi;
use App\User;
use App\MasterSukuCadang;
use App\ListSukuCadangPengajuan;
use App\PelaksanaPengajuan;
use App\JadwalPemeliharaan;
use App\Pemeliharaan;
use App\PelaksanaanPemeliharaan;
use App\ListSukuCadangPemeliharaan;
use App\Mail;

class Home extends Controller
{
    public function __construct()
    {
        $this->middleware('teknisi');
    }

    public function index(){
        $id_teknisi = User::where('id', \Auth::user()->id)->first()->teknisi->id_teknisi;
        //dd($id_teknisi);
        $pelaksanapengajuan = PelaksanaPengajuan::where('id_teknisi',$id_teknisi)->get();
        $pengajuan = Pengajuan::all();
        $teknisi = Teknisi::all();
        $sukucadang = MasterSukuCadang::all();
        $listsukucadang = ListSukuCadangPengajuan::all();
        return view('teknisi/index',compact('pengajuan','teknisi','id_teknisi','listsukucadang','sukucadang', 'pelaksanapengajuan'));
    }

    public function Pemeliharaan(){
        $id_teknisi = User::where('id', \Auth::user()->id)->first()->teknisi->id_teknisi;
        $jadwalpemeliharaan = JadwalPemeliharaan::where('id_teknisi',$id_teknisi)->where('is_year', 0)->get();
        $pemeliharaan = Pemeliharaan::all();
        return view('teknisi/pemeliharaan',compact('jadwalpemeliharaan','pemeliharaan','id_teknisi'));
    }

    public function Kalibrasi(){
        $id_teknisi = User::where('id', \Auth::user()->id)->first()->teknisi->id_teknisi;
        $jadwalpemeliharaan = JadwalPemeliharaan::where('id_teknisi',$id_teknisi)->get();
        $pemeliharaan = Pemeliharaan::all();
        return view('teknisi/kalibrasi',compact('jadwalpemeliharaan','pemeliharaan','id_teknisi'));
    }

    public function KonfirmasiAnalisis($id_pengajuan = null){
        $pengajuan = Pengajuan::where('id_pengajuan',$id_pengajuan)->first();
        $sukucadang = MasterSukuCadang::all();
        $listsukucadang = ListSukuCadangPengajuan::where('id_pengajuan',$id_pengajuan)->get();
        return view('teknisi/konfirmasianalisis',compact('pengajuan','sukucadang','listsukucadang','id_pengajuan'));
    }

    public function UpdatePemeliharaan($id_pemeliharaan){
        $pemeliharaan = Pemeliharaan::where('id_pemeliharaan',$id_pemeliharaan)->first();
        $pelaksanapemeliharaan = PelaksanaanPemeliharaan::where('id_pemeliharaan',$id_pemeliharaan)->get();
        $sukucadang = MasterSukuCadang::all();
        $listsukucadang = ListSukuCadangPemeliharaan::where('id_pemeliharaan',$id_pemeliharaan)->get();
        $teknisi = Teknisi::all();
        return view('teknisi/updatepemeliharaan',compact('pemeliharaan','pelaksanapemeliharaan','sukucadang','listsukucadang','teknisi'));
    }

    public function HapusTeknisiPemeliharaan($id){
        PelaksanaanPemeliharaan::where('id',$id)->delete();
        return back();
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
        return view('teknisi/pesan',compact('user','id_user','notread'));
    }

    public function SubmitTambahPemeliharaan(Request $request){
        $jadwalpemeliharaan = JadwalPemeliharaan::where('id_jadwal_pemeliharaan',$request->input('id_jadwal_pemeliharaan'))->first();
        $respontime = date('Y-m-d', strtotime(Carbon::now()->toDateTimeString()));
        $respontime = "".$respontime." ".$jadwalpemeliharaan->waktu;

        $pemeliharaan = new Pemeliharaan;
        $pemeliharaan->id_jadwal_pemeliharaan = $request->input('id_jadwal_pemeliharaan');
        $pemeliharaan->analisis = $request->input('analisis');
        $pemeliharaan->tindakan_perbaikan = $request->input('tindakan_perbaikan');
        $pemeliharaan->waktu_realisasi = Carbon::now();
        $pemeliharaan->respon_time = $respontime;
        $pemeliharaan->save();

        $pelaksanapemeliharaan = new PelaksanaanPemeliharaan;
        $pelaksanapemeliharaan->id_pemeliharaan = $pemeliharaan->id_pemeliharaan;
        $pelaksanapemeliharaan->id_teknisi = $jadwalpemeliharaan->teknisi->id_teknisi;
        $pelaksanapemeliharaan->save();

        $jadwalpemeliharaan->last_work = Carbon::now();
        $jadwalpemeliharaan->save();

        return back();
    }

    public function SubmitMulaiPengajuan(Request $request){
        Pengajuan::where('id_pengajuan', $request->input('id_pengajuan'))
            ->update([
                'step' => 2,
                'respon_time' => Carbon::now()
            ]);
        return back();
    }

    public function SubmitAnalisisTeknisi(Request $request){
        if($request->input('button') == "analisis"){
            if($request->input('step') == 2){
                $data = [
                    'analisis' => $request->input('analisis'),
                    'tindakan_perbaikan' => $request->input('tindakan_perbaikan'),
                    'step' => 3
                ];
            }else{
                $data = [
                    'analisis' => $request->input('analisis'),
                    'tindakan_perbaikan' => $request->input('tindakan_perbaikan')
                ];
            }
        }else{
            $data = [
                'step' => 4
            ];
        }
        Pengajuan::where('id_pengajuan', $request->input('id_pengajuan'))
            ->update($data);
        return back();
    }

    public function SubmitAmbilPengajuan(Request $request){
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
        return back();
    }

    public function SubmitSukuCadang(Request $request){
        $listsukucadang = new ListSukuCadangPengajuan;
        $listsukucadang->id_pengajuan = $request->input('id_pengajuan');
        $listsukucadang->id_suku_cadang = $request->input('id_suku_cadang');
        $listsukucadang->jumlah = $request->input('jumlah');
        $listsukucadang->save();

        return back();
    }

    public function SubmitSukuCadangPemeliharaan(Request $request){
        $listsukucadang = new ListSukuCadangPemeliharaan;
        $listsukucadang->id_pemeliharaan = $request->input('id_pemeliharaan');
        $listsukucadang->id_suku_cadang = $request->input('id_suku_cadang');
        $listsukucadang->jumlah = $request->input('jumlah');
        $listsukucadang->save();

        return back();
    }

    public function SubmitTeknisiPemeliharaan(Request $request){
        $pelaksanaanpemeliharaan = new PelaksanaanPemeliharaan;
        $pelaksanaanpemeliharaan->id_pemeliharaan = $request->input('id_pemeliharaan');
        $pelaksanaanpemeliharaan->id_teknisi = $request->input('id_teknisi');
        $pelaksanaanpemeliharaan->save();

        return back();
    }

    public function SubmitAnalisisPemeliharaan(Request $request){
        if($request->input('button') == 1){
            $pemeliharaan = Pemeliharaan::find($request->input('id_pemeliharaan'));
            $pemeliharaan->step = 1;
            $pemeliharaan->done_time = Carbon::now();
            $pemeliharaan->save();

            return redirect('teknisi/pemeliharaan');
        }else{
            $pemeliharaan = Pemeliharaan::find($request->input('id_pemeliharaan'));
            $pemeliharaan->analisis = $request->input('analisis');
            $pemeliharaan->tindakan_perbaikan = $request->input('tindakan_perbaikan');
            $pemeliharaan->save();
            return back();
        }
    }
}
