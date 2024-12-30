<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anggaran extends Model
{
    protected $table = 'anggarans';
    protected $fillable = ['nilai_anggaran','nilai_realisasi','tahun','sub_kegiatan_id','sub_rincian_obyek_akun_id', 'kode'];

    // sum nilai_anggaran
    public function scopeSumNilaiAnggaran($query)
    {
        return $query->sum('nilai_anggaran');
    }

    // Accessor for nilai_anggaran
    protected function nilaiAnggaran(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp ' . number_format($value, 2, ',', '.'),
            set: fn ($value) => str_replace(['Rp ', '.', ','], ['', '', '.'], $value)
        );
    }

    // Accessor for nilai_realisasi
    protected function nilaiRealisasi(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp ' . number_format($value, 2, ',', '.'),
            set: fn ($value) => str_replace(['Rp ', '.', ','], ['', '', '.'], $value)
        );
    }

    // berikan nilai asli
    public function getRawNilaiAnggaranAttribute()
    {
        return $this->attributes['nilai_anggaran'];
    }

    public function getRawNilaiRealisasiAttribute()
    {
        return $this->attributes['nilai_realisasi'];
    }

    public function subKegiatan(): BelongsTo
    {
        return $this->belongsTo(SubKegiatan::class, 'sub_kegiatan_id');
    }

    public function subRincianObyekAkun()
    {
        return $this->belongsTo(SubRincianObyekAkun::class);
    }

    public function realisasi()
    {
        return $this->hasMany(Realisasi::class);
    }
}
