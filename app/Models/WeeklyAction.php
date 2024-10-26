<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekly_goal_id',
        'description',
    ];
}
