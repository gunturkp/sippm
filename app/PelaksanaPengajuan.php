<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PelaksanaPengajuan extends Model
{
    protected $fillable = [
        'id_teknisi',
        'id_pengajuan',
    ];

    protected $table = 'tbl_pelaksanaan_pengajuan';

    protected $primaryKey = "id";
    
    public function teknisi(){
        return $this->belongsTo('App\Teknisi', 'id_teknisi', 'id_teknisi');
    }

    public function pengajuan(){
        return $this->belongsTo('App\Pengajuan', 'id_pengajuan', 'id_pengajuan');
    }
}
