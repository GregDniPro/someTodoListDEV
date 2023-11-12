<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory; //TODO what does?

    protected $fillable = [
        'uuid',
        'title',
        'description',
        'status',
        'priority',
        'completed_at',
        'parent_id'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    //    protected $relations = [
    //        'child-tasks'
    //    ];

    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function childTasks()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }
}
