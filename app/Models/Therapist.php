<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Therapist extends Model
{
    protected $fillable = ['name', 'gender', 'bio', 'experience_years', 'rating', 'active'];

    protected function casts(): array
    {
        return [
            'rating' => 'float',
            'active' => 'boolean',
        ];
    }
}
