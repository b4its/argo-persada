<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'action', 'description', 'oldData', 'newData', 'ip_address', 'user_agent'])]
class LogActivities extends Model
{
    protected $table = 'log_activities';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}