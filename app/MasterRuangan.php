<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterRuangan extends Model
{
    protected $fillable = [
        'id_tipe_ruangan',
        'nama_ruangan',
        'jarak',
    ];

    protected $table = 'master_ruangan';

    protected $primaryKey = "id_ruangan";

    public function tiperuangan(){
        return $this->belongsTo('App\MasterTipeRuangan', 'id_tipe_ruangan');
    }
}
