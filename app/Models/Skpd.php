<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skpd extends Model
{
    protected $table = 'skpds';
    protected $fillable = ['kode', 'nama', 'urusan_pelaksana_id'];

    public function urusanPelaksana(): BelongsTo
    {
        return $this->belongsTo(UrusanPelaksana::class);
    }

    public function subSkpds(): HasMany
    {
        return $this->hasMany(SubSkpd::class);
    }
}
