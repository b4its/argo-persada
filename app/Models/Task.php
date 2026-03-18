<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'role', 'description', 'due_date', 'status'])]
class Task extends Model
{
    protected $table = 'task';

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'status' => 'integer',
        ];
    }

    public function taskActivities(): HasMany
    {
        return $this->hasMany(TaskActivity::class);
    }
}