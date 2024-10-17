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
        'glasses',
        'child_birth',
        'inpatient',
        'outpatient',
        'total_coverage',
        'glasses_uncover',
        'child_birth_uncover',
        'inpatient_uncover',
        'outpatient_uncover',
        'total_uncoverage',
        'status',
        'medical_proof',
    ];
}
