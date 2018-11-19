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
use App\Pemeliharaan;

class ExportPemeliharaan implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;
    public function query()
    {
        return Pemeliharaan::query();
    }

    public function map($pemeliharaan):array{
        $merkbarang = $pemeliharaan->jadwalpemeliharaan->inventory->merk_barang;
        $spesifikasi = $pemeliharaan->jadwalpemeliharaan->inventory->spesifikasi;
        $barang = $merkbarang." ".$spesifikasi;
        if ($pemeliharaan->jadwalpemeliharaan->is_year == 1){
            $kalibrasi = 'Ya';
        } else {
            $kalibrasi = 'Tidak';
        };
        if($pemeliharaan->jadwalpemeliharaan->hari == 1){
            $hari = 'Senin';
        }
        elseif($pemeliharaan->jadwalpemeliharaan->hari == 2){
            $hari = 'Selasa';
        }
        elseif($pemeliharaan->jadwalpemeliharaan->hari == 3){
            $hari = 'Rabu';
        }
        elseif($pemeliharaan->jadwalpemeliharaan->hari == 4){
            $hari = 'Kamis';
        }
        elseif($pemeliharaan->jadwalpemeliharaan->hari == 5){
            $hari = "Jum'at";
        }
        elseif($pemeliharaan->jadwalpemeliharaan->hari == 6){
            $hari = 'Sabtu';
        }
        elseif($pemeliharaan->jadwalpemeliharaan->hari == 7){
            $hari = 'Minggu';
        };
        if(!empty($pemeliharaan->waktu_realisasi)){
            $realisasi = $pemeliharaan->waktu_realisasi;
        }else{
            $realisasi = "Belum dikerjakan";
        }
        return [
            $pemeliharaan->id_jadwal_pemeliharaan,
            $pemeliharaan->jadwalpemeliharaan->inventory->nama_alat,
            $pemeliharaan->jadwalpemeliharaan->inventory->ruangan->nama_ruangan,
            $hari,
            $kalibrasi,
            $realisasi,
            $pemeliharaan->analisis,
            $pemeliharaan->tindakan_perbaikan,
            $pemeliharaan->pelaksanapemeliharaan->teknisi->user->name,
        ];
    }
    public function headings(): array
    {
        return [
            'No. Jadwal',
            'Alat',
            'Ruangan',
            'Jadwal Hari',
            'Kalibrasi',
            'Waktu Realisasi',
            'Analisis',
            'Tind. Perbaikan',
            'Teknisi',
        ];
    }
}