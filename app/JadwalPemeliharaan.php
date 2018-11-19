<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JadwalPemeliharaan extends Model
{
    protected $fillable = [
        'id_teknisi',
        'id_inventory',
        'hari',
        'waktu',
    ];

    protected $table = 'tbl_jadwal_pemeliharaan';

    protected $primaryKey = "id_jadwal_pemeliharaan";

    public function teknisi(){
        return $this->belongsTo('App\Teknisi', 'id_teknisi');
    }

    public function inventory(){
        return $this->belongsTo('App\MasterInventory', 'id_inventory');
    }

    public function pemeliharaan(){
        return $this->belongsTo('App\Pemeliharaan', 'id_jadwal_pemeliharaan', 'id_jadwal_pemeliharaan');
    }
}
