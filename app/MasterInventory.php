<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterInventory extends Model
{
    protected $fillable = [
        'id_ruangan',
        'id_tipe_inventory',
        'kode_alat',
        'nama_alat',
        'merk_barang',
        'spesifikasi',
        'serial_number',
        'usiateknis',
        'hargaperolehan',
        'tahunpengadaan',
        'distributor',
        'servicemanual',
        'tahunpensiun',
        'nilai_kalibrasi',
        'aic',
    ];

    protected $table = 'master_inventory';

    protected $primaryKey = "id_inventory";
    
    public function tipeinventory(){
        return $this->belongsTo('App\MasterTipeInventory', 'id_tipe_inventory');
    }

    public function ruangan(){
        return $this->belongsTo('App\MasterRuangan', 'id_ruangan');
    }
}
