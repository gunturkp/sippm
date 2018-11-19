<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PelaksanaanPemeliharaan extends Model
{
    protected $fillable = [
        'id_teknisi',
        'id_pemeliharaan',
    ];

    protected $table = 'tbl_pelaksanaan_pemeliharaan';

    protected $primaryKey = "id";

    public function teknisi(){
        return $this->belongsTo('App\Teknisi', 'id_teknisi');
    }
}
