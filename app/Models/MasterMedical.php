<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterMedical extends Model
{
    use HasFactory;

    protected $table = 'master_medical_type';
    protected $fillable = [
        'id',
        'medical_type',
        'name',
    ];
}
