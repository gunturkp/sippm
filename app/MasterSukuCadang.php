<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterSukuCadang extends Model
{
    protected $fillable = [
        'nama_suku_cadang',
        'jumlah',
    ];

    protected $table = 'master_suku_cadang';

    protected $primaryKey = "id_suku_cadang";
}
