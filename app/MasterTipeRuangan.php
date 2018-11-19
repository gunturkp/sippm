<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterTipeRuangan extends Model
{
    protected $fillable = [
        'nama_tipe_ruangan',
    ];

    protected $table = 'master_tipe_ruangan';

    protected $primaryKey = "id_tipe_ruangan";

    public function ruangan(){
        return $this->hasMany('App\MasterRuangan', 'id_ruangan');
    }
}
