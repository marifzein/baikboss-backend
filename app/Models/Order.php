<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_code', 'user_id', 'wa_number', 'service_type',
        'origin_label', 'origin_lat', 'origin_lng', 'origin_kecamatan_id',
        'dest_type', 'dest_tarif_id', 'dest_wilayah_id', 'dest_label', 'dest_lat', 'dest_lng',
        'schedule_date', 'schedule_time',
        'tarif_amount', 'items_amount', 'total_amount',
        'notes', 'status',
    ];

    protected $casts = [
        'schedule_date' => 'date:Y-m-d',
        'tarif_amount' => 'integer',
        'items_amount' => 'integer',
        'total_amount' => 'integer',
        'origin_lat' => 'float',
        'origin_lng' => 'float',
        'dest_lat' => 'float',
        'dest_lng' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function destTarif(): BelongsTo
    {
        return $this->belongsTo(TarifKecamatan::class, 'dest_tarif_id');
    }

    public function destWilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'dest_wilayah_id');
    }

    public function originKecamatan(): BelongsTo
    {
        return $this->belongsTo(TarifKecamatan::class, 'origin_kecamatan_id');
    }
}
