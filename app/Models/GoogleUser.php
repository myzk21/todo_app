<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'google_id',
        'access_token',
        'refresh_token',
        'expires',
        'user_id',
    ];

    protected $hidden = [
        'google_id',
        'access_token',
        'refresh_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
