<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class CompanyInternal extends Model
{
    //
    protected $table = 'company_internal';


    protected $fillable = [
        'name',
        'singkatan',
        'alamat',
        'phone_number',
        'is_ppn',
        'gambar',
    ];


    public function pesanans(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }


}
