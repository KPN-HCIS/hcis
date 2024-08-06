<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taksi extends Model
{
    protected $fillable = [
        'id_vt',
        'nama',
        'no_vt',
        'no_sppd',
        'user_id',
        'unit',
        'sppd_bt',
        'nom_vt',
        'keeper_vt',
    ];
    protected $table = 'vt_transaction';
}
