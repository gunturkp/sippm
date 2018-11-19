<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pemeliharaan extends Model
{
    protected $fillable = [
        'id_jadwal_pemeliharaan',
        'waktu_realisasi',
        'respon_time',
        'done_time',
        'step',
        'analisis',
        'tindakan_perbaikan',
    ];

    protected $table = 'tbl_pemeliharaan';

    protected $primaryKey = "id_pemeliharaan";

    public function jadwalpemeliharaan(){
        return $this->belongsTo('App\JadwalPemeliharaan', 'id_jadwal_pemeliharaan');
    }

    public function sukucadang(){
        return $this->belongsTo('App\ListSukuCadangPemeliharaan', 'id_pemeliharaan', 'id_pemeliharaan');
    }

    public function pelaksanapemeliharaan(){
        return $this->belongsTo('App\PelaksanaanPemeliharaan', 'id_pemeliharaan');
        return $this->hasMany('App\PelaksanaanPemeliharaan', 'id_pemeliharaan', 'id_jadwal_pemeliharaan');
    }
}
