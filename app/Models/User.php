<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function keranjang(): HasMany
    {
        return $this->hasMany(Keranjang::class);
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }

    public function logActivities(): HasMany
    {
        return $this->hasMany(LogActivities::class);
    }

    public function createdTaskActivities(): HasMany
    {
        return $this->hasMany(TaskActivity::class, 'created_user_id');
    }

    public function updatedTaskActivities(): HasMany
    {
        return $this->hasMany(TaskActivity::class, 'updated_user_id');
    }

    public function queueKeranjang(): HasMany
    {
        return $this->hasMany(QueueKeranjang::class);
    }

    public function queueDeletesAsOwner(): HasMany
    {
        return $this->hasMany(QueueDelete::class, 'owner_id');
    }

    public function queueDeletesAsUser(): HasMany
    {
        return $this->hasMany(QueueDelete::class, 'user_id');
    }
}