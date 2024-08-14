<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        // Kolom-kolom lainnya,
        'access_menu','id','employee_id', 'fullname', 'gender', 'email', 'group_company',
        'designation', 'job_level', 'company_name', 'contribution_level_code',
        'work_area_code', 'office_area', 'manager_l1_id', 'manager_l2_id',
        'employee_type', 'unit', 'date_of_joining', 'users_id','personal_email','personal_mobile_number','date_of_birth','place_of_birth'
        ,'nationality','religion','marital_status','citizenship_status','ethnic_group','homebase','current_address','current_city'
        ,'permanent_address','permanent_city','blood_group','tax_status','bpjs_tk','bpjs_ks','ktp','kk','npwp','mother_name','bank_name'
        ,'bank_account_number','bank_name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
    public function businessTrip()
    {
        return $this->belongsTo(BusinessTrip::class, 'user_id', 'id');
    }
    public function taksi()
    {
        return $this->belongsTo(Taksi::class, 'user_id', 'id');
    }
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'user_id', 'id');
    }
    public function tiket()
    {
        return $this->belongsTo(Tiket::class, 'user_id', 'id');
    }
    public function goal()
    {
        return $this->belongsTo(Goal::class, 'employee_id', 'employee_id');
    }

    public function approvalRequest()
    {
        return $this->hasMany(ApprovalRequest::class, 'employee_id', 'employee_id');
    }

    public function approvalLayer()
    {
        return $this->hasMany(ApprovalLayer::class, 'employee_id', 'id');
    }
    public function approvalManager()
    {
        return $this->hasMany(ApprovalRequest::class, 'employee_id', 'current_approval_id');
    }
    public function creatorApproverLayer()
    {
        return $this->hasMany(ApprovalLayer::class, 'creator_id', 'id');
    }
    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'bisnis_unit', 'group_company')
                ->whereRaw("FIND_IN_SET('bisnis_unit', group_company)");
    }
    public static function getUniqueGroupCompanies()
    {
        // Ambil data group_company yang unik dari tabel employee
        return self::select('group_company')
            ->distinct()
            ->pluck('group_company');
    }
}
