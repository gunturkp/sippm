<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListSukuCadangPemeliharaan extends Model
{
    protected $fillable = [
        'id_pemeliharaan',
        'id_suku_cadang',
        'jumlah',
        'is_approve',
    ];

    protected $table = 'tbl_list_suku_cadang_pemeliharaan';

    protected $primaryKey = "id";

    public function inventory(){
        return $this->belongsTo('App\MasterSukuCadang', 'id_suku_cadang');
    }
}
