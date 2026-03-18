<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'images_url', 'phone_number'])]
class Profile extends Model
{
    protected $table = 'profile';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}