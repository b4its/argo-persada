<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Saldo extends Model
{
    //
    protected $table = 'saldo';


    protected $fillable = [
        'kas_harian_id',
        'saldo_awal',
        'saldo_akhir',
    ];

        public function kasHarian(): HasMany
    {
        return $this->hasMany(KasHarian::class, 'kas_harian_id');
    }
}


