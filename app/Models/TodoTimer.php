<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TodoTimer extends Model
{
    protected $fillable = [
        'started_at',
        'elapsed_time_at_stop',
        'status',
        'user_id',
        'todo_id',
    ];

    public function todo() {
        return $this->belongsTo(Todo::class, 'todo_id', 'id');
    }
}
