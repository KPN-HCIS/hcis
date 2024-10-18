<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class HealthCoverage extends Model
{
    use HasFactory,  HasUuids;
    use SoftDeletes;
    protected $primaryKey = 'usage_id';
    protected $table = 'health_coverage_usage';

    protected $fillable = [
        'usage_id',
        'employee_id',
        'no_medic',
        'no_invoice',
        'hospital_name',
        'patient_name',
        'disease',
        'date',
        'coverage_detail',
        'period',
        'medical_type',
        'balance',
        'balance_uncoverage',
        'balance_verif',
        'verif_by',
        'created_by',
        'status',
        'medical_proof',
    ];
}
