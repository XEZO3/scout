<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class admins extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [        
        'username',
        'name',
        'password',
        'level'
    ];


    protected $casts = [
        // 'password' => 'hashed',
    ];
}
