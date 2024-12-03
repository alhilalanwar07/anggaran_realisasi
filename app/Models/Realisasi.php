<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
            get: fn ($value) => 'Rp ' . number_format($value, 0, ',', '.'),
            set: fn ($value) => str_replace(['Rp ', '.', ','], '', $value)
        );
    }

    public function anggaran()
    {
        return $this->belongsTo(Anggaran::class);
    }
}
