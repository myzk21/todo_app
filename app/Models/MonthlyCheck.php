<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_goal_id',
        'review',
        'description',
    ];
}
