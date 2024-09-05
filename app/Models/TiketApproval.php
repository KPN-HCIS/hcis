<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TiketApproval extends Model
{
    use HasFactory, HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'tkt_id',
        'role_id',
        'role_name',
        'employee_id',
        'layer',
        'approval_status',
        'approved_at',
    ];
    protected $table = 'tkt_approvals';
}
