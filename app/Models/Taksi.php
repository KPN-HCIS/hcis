<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taksi extends Model
{
    protected $fillable = [
        'id',
        'no_vt',
        'no_sppd',
        'user_id',
        'unit',
        'nominal_vt',
        'keeper_vt',
    ];
    protected $table = 'vt_transaction';
}
