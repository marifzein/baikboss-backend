<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TarifKecamatan extends Model
{
    protected $table = 'tarif_kecamatan';

    protected $fillable = [
        'kecamatan', 'kota', 'provinsi', 'tarif', 'tarif_luar', 'aktif',
    ];

    protected $casts = [
        'tarif' => 'integer',
        'tarif_luar' => 'integer',
        'aktif' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'dest_tarif_id');
    }
}
