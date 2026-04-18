<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class KasHarian extends Model
{
    //
    protected $table = 'kas_harian';


    protected $fillable = [
        'saldo_id',
        'company_internal_id',
        'user_id',
        'pesanan_id',
        'keterangan'
    ];

    public function saldo(): HasMany
    {
        return $this->hasMany(Saldo::class, 'saldo_id');
    }

    public function companyInternal(): HasMany
    {
        return $this->hasMany(CompanyInternal::class, 'company_internal_id');
    }

    public function user(): HasMany
    {
        return $this->hasMany(User::class, 'user_id');
    }
    
    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'pesanan_id');
    }
}


            // $table->foreignId('saldo_id')->nullable()->constrained('saldo')->onDelete('cascade');
            // $table->foreignId('pesanan_id')->nullable()->constrained('pesanan_id')->onDelete('cascade');
            // $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // $table->foreignId('pesanan_id')->nullable()->constrained('pesanan')->onDelete('cascade');
            // $table->string('keterangan')->nullable();