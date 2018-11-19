<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teknisi extends Model
{
    protected $fillable = [
        'user_id',
    ];

    protected $table = 'teknisi';

    protected $primaryKey = "id_teknisi";

    public function user(){
        return $this->hasOne('App\User', 'id','user_id');
    }
}
