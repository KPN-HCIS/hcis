<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Tiket extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    public function businessTrip()
    {
        return $this->belongsTo(BusinessTrip::class, 'user_id', 'user_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'user_id', 'id');
    }
    public function ticketApproval()
    {
        return $this->belongsTo(TiketApproval::class, 'id', 'tkt_id');
    }
    public function getManager1FullnameAttribute()
    {
        // Get the associated BusinessTrip record
        $businessTrip = $this->businessTrip;
        if ($businessTrip && $businessTrip->manager1) {
            return $businessTrip->manager1->fullname;
        }
        return '-';
    }

    // Relationship to Employee through BusinessTrip for Manager 2
    public function getManager2FullnameAttribute()
    {
        // Get the associated BusinessTrip record
        $businessTrip = $this->businessTrip;
        if ($businessTrip && $businessTrip->manager2) {
            return $businessTrip->manager2->fullname;
        }
        return '-';
    }
    public function latestApprovalL1()
    {
        return $this->hasOne(TiketApproval::class, 'tkt_id', 'id')
            ->where('layer', 1)
            ->where('approval_status', 'Pending L2')
            ->latest('approved_at');
    }
    public function latestApprovalL2()
    {
        return $this->hasOne(TiketApproval::class, 'tkt_id', 'id')
            ->where('layer', 2)
            ->where('approval_status', 'Approved')
            ->latest('approved_at');
    }


    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'no_tkt',
        'no_sppd',
        'user_id',
        'unit',
        'jk_tkt',
        'np_tkt',
        'noktp_tkt',
        'tlp_tkt',
        'dari_tkt',
        'ke_tkt',
        'tgl_brkt_tkt',
        'tgl_plg_tkt',
        'jam_brkt_tkt',
        'jam_plg_tkt',
        'jenis_tkt',
        'type_tkt',
        'ket_tkt',
        'approval_status',
        'tkt_only',
        'jns_dinas_tkt',
        'contribution_level_code',
    ];
    protected $table = 'tkt_transactions';

    public function getRouteKey()
    {
        return encrypt($this->getKey());
    }

    public static function findByRouteKey($key)
    {
        try {
            $id = decrypt($key);
            Log::info('Decrypted ID:', ['id' => $id]); // Log the decrypted ID
            return self::findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Decryption Error:', ['message' => $e->getMessage()]);
            abort(404);
        }
    }


}
