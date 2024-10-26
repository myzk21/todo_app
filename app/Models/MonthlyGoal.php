<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'due',
    ];
    public function monthlyCheck() {
        return $this->hasOne(MonthlyCheck::class);
    }
    public function monthlyAction() {
        return $this->hasOne(MonthlyAction::class);
    }
}
