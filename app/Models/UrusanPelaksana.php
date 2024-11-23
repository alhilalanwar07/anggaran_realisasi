<?php

namespace App\Models;

use App\Models\Urusan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UrusanPelaksana extends Model
{
    protected $table = 'urusan_pelaksanas';
    protected $fillable = ['kode', 'nama', 'urusan_id'];

    public function urusan(): BelongsTo
    {
        return $this->belongsTo(Urusan::class);
    }

    // skpd
    public function skpd(): HasMany
    {
        return $this->hasMany(Skpd::class);
    }
}
