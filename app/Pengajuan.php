<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    protected $fillable = [
        'user_id',
        'id_inventory',
        'respon_time',
        'done_time',
        'step',
        'keluhan',
        'analisis',
        'tindakan_perbaikan',
    ];

    protected $table = 'tbl_pengajuan';

    protected $primaryKey = "id_pengajuan";
    
    public function inventory(){
        return $this->belongsTo('App\MasterInventory', 'id_inventory');
    }
    
    public function user(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function pelaksanapengajuan(){
        return $this->belongsTo('App\PelaksanaPengajuan', 'id_pengajuan', 'id_pengajuan');
    }

    public function suku_cadang(){
        return $this->belongsTo('App\ListSukuCadangPengajuan', 'id_pengajuan', 'id_pengajuan');
    }
}