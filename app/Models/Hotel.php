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
        'no_htl', 'user_id', 'unit', 'no_sppd', 'nama_htl', 'lokasi_htl', 'jmlkmr_htl', 'bed_htl', 'tgl_masuk_htl', 'tgl_keluar_htl', 'start_date', 'end_date', 'date_required', 'detail_ca', 'total_ca', 'total_hari', 'total_real', 'total_cost', 'approval_status', 'approval_sett', 'approval_extend'
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
}
