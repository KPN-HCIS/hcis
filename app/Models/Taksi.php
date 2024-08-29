<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Taksi extends Model
{
    use HasFactory, HasUuids;
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'user_id', 'id');
    }
    public function businessTrip()
    {
        return $this->belongsTo(BusinessTrip::class, 'user_id', 'user_id');
    }
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
