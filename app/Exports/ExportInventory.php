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
use App\MasterInventory;

class ExportInventory implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;
    public function query()
    {
        return MasterInventory::query();
    }

    public function map($inventory):array{
        if($inventory->servicemanual == 0){
            $sm = "Tidak";
        }
        else {
            $sm = "Ya";
        }
        return [
            $inventory->id_inventory,
            $inventory->ruangan->nama_ruangan,
            $inventory->tipeinventory->nama_tipe_inventory,
            $inventory->nama_alat,
            $inventory->merk_barang,
            $inventory->spesifikasi,
            $inventory->serial_number,
            $inventory->kode_alat,
            $inventory->usiateknis,
            $inventory->hargaperolehan,
            $inventory->tahunpengadaan,
            $inventory->distributor,
            $sm,
            $inventory->tahunpensiun,
            $inventory->nilai_kalibrasi,
            $inventory->aic,
        ];
    }
    public function headings(): array
    {
        return [
            'No.',
            'Ruangan',
            'Tipe Inventory',
            'Nama Inventory',
            'Merk',
            'Spesifikasi',
            'Serial Number',
            'Kode',
            'Usia Teknis',
            'Harga Perolehan',
            'Tahun Pengadaan',
            'Distributor',
            'Service Manual',
            'Usia Pakai',
            'Nilai Kalibrasi',
            'AIC',
        ];
    }
}