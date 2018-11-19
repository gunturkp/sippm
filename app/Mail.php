<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    protected $fillable = [
        'user_id_sender',
        'user_id_receiver',
        'message',
        'is_read',
    ];

    protected $table = 'mail';

    protected $primaryKey = "id";

    public function sender(){
        return $this->hasOne('App\User', 'id','user_id_sender');
    }

    public function receiver(){
        return $this->hasOne('App\User', 'id','user_id_receiver');
    }
}
