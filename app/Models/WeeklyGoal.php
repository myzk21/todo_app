<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'due',
    ];
    public function weeklyCheck() {
        return $this->hasOne(WeeklyCheck::class);
    }
    public function weeklyAction() {
        return $this->hasOne(WeeklyAction::class);
    }
}
