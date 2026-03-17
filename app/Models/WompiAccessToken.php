<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WompiAccessToken extends Model
{
    protected $table = 'wompi_access_tokens';

    protected $fillable = [
        'access_token',
        'expires_in',
        'token_type',
        'scope',
        'obtained_at',
    ];

    protected $casts = [
        'obtained_at' => 'datetime',
    ];
}

