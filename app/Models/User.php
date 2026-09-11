<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'phone', 'email', 'phone_verified_at'];

    protected $hidden = ['remember_token'];

    protected function casts(): array
    {
        return [
            'phone_verified_at' => 'datetime',
        ];
    }
}
