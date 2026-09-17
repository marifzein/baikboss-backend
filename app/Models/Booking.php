<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'booking_code', 'category', 'item_key', 'item_label',
        'duration_hours', 'price', 'ongkir', 'total',
        'user_id', 'therapist_id', 'therapist_name',
        'address_id', 'address_text', 'address_lat', 'address_lng',
        'schedule_date', 'schedule_time', 'notes', 'status',
    ];

    protected function casts(): array
    {
        return [
            'schedule_date' => 'date:Y-m-d',
            'price' => 'integer',
            'ongkir' => 'integer',
            'total' => 'integer',
            'duration_hours' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
