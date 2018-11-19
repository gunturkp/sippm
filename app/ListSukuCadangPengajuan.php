<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListSukuCadangPengajuan extends Model
{
    protected $fillable = [
        'id_pengajuan',
        'id_suku_cadang',
        'jumlah',
        'is_approve',
    ];

    protected $table = 'tbl_list_suku_cadang_pengajuan';

    protected $primaryKey = "id";

    public function inventory(){
        return $this->belongsTo('App\MasterSukuCadang', 'id_suku_cadang');
    }
    public function pengajuan(){
        return $this->belongsTo('App\Pengajuan', 'id_pengajuan', 'id_pengajuan');
    }
}
