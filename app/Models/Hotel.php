<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Hotel extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'no_htl',
        'user_id',
        'unit',
        'no_sppd',
        'nama_htl',
        'lokasi_htl',
        'jmlkmr_htl',
        'bed_htl',
        'tgl_masuk_htl',
        'tgl_keluar_htl',
        'start_date',
        'end_date',
        'date_required',
        'detail_ca',
        'total_ca',
        'total_hari',
        'total_real',
        'total_cost',
        'approval_status',
        'approval_sett',
        'approval_extend',
        'hotel_only',
        'reject_info',
        'manager_l1_id',
        'manager_l2_id',
        'contribution_level_code',
    ];
    protected $table = 'htl_transactions';

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }

    public function getRouteKey()
    {
        return encrypt($this->getKey());
    }

    public static function findByRouteKey($key)
    {
        try {
            $id = decrypt($key);
            return self::findOrFail($id);
        } catch (\Exception $e) {
            abort(404);
        }
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'user_id', 'id');
    }
    public function businessTrip()
    {
        return $this->belongsTo(BusinessTrip::class, 'user_id', 'user_id');
    }

    public function latestApprovalL1()
    {
        return $this->hasOne(HotelApproval::class, 'htl_id', 'id')
            ->where('layer', 1)
            ->where('approval_status', 'Pending L2')
            ->latest('approved_at');
    }
    public function latestApprovalL2()
    {
        return $this->hasOne(HotelApproval::class, 'htl_id', 'id')
            ->where('layer', 2)
            ->where('approval_status', 'Approved')
            ->latest('approved_at');
    }

    public function latestApprovalL1Id()
    {
        return $this->belongsTo(Employee::class, 'user_id', 'id')->select('manager_l1_id');
    }

    public function getManagerL1Fullname()
    {
        $managerL1Id = $this->latestApprovalL1Id?->manager_l1_id;
        if ($managerL1Id) {
            return Employee::where('employee_id', $managerL1Id)->value('fullname') ?? '-';
        }
        return '-';
    }
    public function latestApprovalL2Id()
    {
        return $this->belongsTo(Employee::class, 'user_id', 'id')->select('manager_l2_id');
    }

    public function getManagerL2Fullname()
    {
        $managerL2Id = $this->latestApprovalL2Id?->manager_l2_id;
        if ($managerL2Id) {
            return Employee::where('employee_id', $managerL2Id)->value('fullname') ?? '-';
        }
        return '-';
    }
    public function latestApprovalL1Name()
    {
        return $this->belongsTo(Employee::class, 'manager_l1_id', 'employee_id')->select('fullname');
    }
    public function latestApprovalL2Name()
    {
        return $this->belongsTo(Employee::class, 'manager_l2_id', 'employee_id')->select('fullname');
    }
    public function hotelApproval()
    {
        return $this->hasOne(HotelApproval::class, 'htl_id', 'id');
    }
}
