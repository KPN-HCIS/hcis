<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BTApproval extends Model
{
    use HasFactory;
    public function businessTrip()
    {
        return $this->belongsTo(BusinessTrip::class, 'bt_id', 'id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'bt_id',
        'role_id',
        'role_name',
        'layer',
        'approval_status',
        'approved_at',
        'employee_id',

    ];
    protected $table = 'bt_approval';
}
