<?php

namespace App\Exports;
use App\User;

use App\Http\Controllers\Admin;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Pengajuan;

class UsersExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;
    public function query()
    {
        return Pengajuan::query();
    }

    public function map($pengajuan):array{
        $merkbarang = $pengajuan->inventory->merk_barang;
        $spesifikasi = $pengajuan->inventory->spesifikasi;
        $barang = $merkbarang." ".$spesifikasi;
        $create_time = strtotime($pengajuan->created_at);
        $respon_time = strtotime($pengajuan->respon_time);
        $interval = date('H:i',($respon_time - $create_time));
        $done_time = strtotime($pengajuan->done_time);
        $done_at = date('H:i',($done_time-$create_time));
        if(!empty($pengajuan->suku_cadang->inventory->nama_suku_cadang)){
            $sukucadang = $pengajuan->suku_cadang->inventory->nama_suku_cadang;
        } else {
            $sukucadang = '-';
        }
        $start_date = new \DateTime($pengajuan->created_at);
        $since_start = $start_date->diff(new \DateTime($pengajuan->respon_time));
        switch (true){
            case $since_start->h < 10:
                $hoursdiff = "0".$since_start->h;
                break;
            case $since_start->i >= 10:
                $hoursdiff = $since_start->h;
                break;
        }switch (true){
            case $since_start->i < 10:
                $minutesdiff = "0".$since_start->i;
                break;
            case $since_start->i >= 10:
                $minutesdiff = $since_start->i;
                break;
        }
        $since_start2 = $start_date->diff(new \DateTime($pengajuan->done_time));
        switch (true){
            case $since_start2->h < 10:
                $hoursdiff2 = "0".$since_start2->h;
                break;
            case $since_start2->i >= 10:
                $hoursdiff2 = $since_start2->h;
                break;
        }switch (true){
            case $since_start2->i < 10:
                $minutesdiff2 = "0".$since_start2->i;
                break;
            case $since_start2->i >= 10:
                $minutesdiff2 = $since_start2->i;
                break;
        }
        if(!empty($pengajuan->respon_time)){
            $rtime = $hoursdiff.":".$minutesdiff;
        }else{
            $rtime = "No response";
        }
        if(!empty($pengajuan->done_time)){
            $dtime = $hoursdiff2.":".$minutesdiff2;
        }else{
            $dtime = "Not done";
        }
        return [
            $pengajuan->id_pengajuan,
            $pengajuan->created_at,
            $pengajuan->inventory->ruangan->nama_ruangan,
            $pengajuan->respon_time,
            $pengajuan->done_time,
            $rtime,
            $dtime,
            $barang,
            $pengajuan->keluhan,
            $pengajuan->analisis,
            $pengajuan->tindakan_perbaikan,
            $sukucadang,
            $pengajuan->pelaksanapengajuan->teknisi->user->name,
        ];
    }
    public function headings(): array
    {
        return [
            'No.',
            'Waktu Order',
            'Ruangan',
            'Waktu Respon',
            'Waktu Selesai',
            'Respon Time',
            'Down Time',
            'Alat',
            'Keluhan',
            'Analisis',
            'Tind. Perbaikan',
            'Suku Cadang',
            'Teknisi',
        ];
    }
}