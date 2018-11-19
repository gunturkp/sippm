<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterTipeInventory extends Model
{
    protected $fillable = [
        'nama_tipe_inventory',
    ];

    protected $table = 'master_tipe_inventory';

    protected $primaryKey = "id_tipe_inventory";
}
