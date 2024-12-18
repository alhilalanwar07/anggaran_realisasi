<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Realisasi extends Model
{
    protected $fillable = [
        'anggaran_id',
        'nilai_realisasi',
        'tahun',
        'kode',
    ];

    protected function nilaiRealisasi(): Attribute
    {
        return Attribute::make(
            get: fn($value) => 'Rp ' . number_format($value, 0, ',', '.'), // Format untuk tampilan
            set: fn($value) => str_replace(['Rp ', '.', ','], '', $value) // Menghapus format saat menyimpan
        );
    }

    // set nilai_realisasi to integer
    public function getRawNilaiRealisasiAttribute()
    {
        return $this->attributes['nilai_realisasi']; // Tetap memberikan akses nilai asli
    }

    public function anggaran(): BelongsTo
    {
        return $this->belongsTo(Anggaran::class);
    }
}
