<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'id_tipe_ruangan',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function teknisi(){
        return $this->hasOne('App\Teknisi', 'user_id', 'id');
    }

    public function tiperuangan(){
        return $this->belongsTo('App\MasterTipeRuangan', 'id_tipe_ruangan', 'id_tipe_ruangan');
    }
}
