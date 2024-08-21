<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ca_approval extends Model
{
    use HasFactory;

    protected $fillable = [
        // Kolom-kolom lainnya,
        'ca_id','role_id','role_name','employee_id','layer','approval_status'
    ];
}
