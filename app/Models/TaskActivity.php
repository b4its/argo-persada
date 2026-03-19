<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['created_user_id', 'updated_user_id', 'task_id', 'note', 'requisition_number', 'delivery_order_number', 'invoice_number', 'pesanan_status'])]
class TaskActivity extends Model
{
    protected $table = 'task_activity';

    protected function casts(): array
    {
        return [
            'pesanan_status' => 'integer',
        ];
    }

    public function createdUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function updatedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_user_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}