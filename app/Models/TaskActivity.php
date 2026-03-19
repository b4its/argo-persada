<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'task_id', 'note', 'requisition_number', 'delivery_order_number', 'invoice_number', 'pesanan_status'])]
class TaskActivity extends Model
{
    protected $table = 'task_activity';

    protected function casts(): array
    {
        return [
            'pesanan_status' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}