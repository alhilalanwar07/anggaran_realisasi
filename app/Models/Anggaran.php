<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Anggaran extends Model
{
    protected $table = 'anggarans';
    protected $fillable = ['nilai_anggaran','nilai_realisasi','tahun','sub_kegiatan_id','sub_rincian_obyek_akun_id', 'kode'];

    // Accessor for nilai_anggaran
    protected function nilaiAnggaran(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp ' . number_format($value, 0, ',', '.'),
            set: fn ($value) => str_replace(['Rp ', '.', ','], '', $value)
        );
    }

    // Accessor for nilai_realisasi
    protected function nilaiRealisasi(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp ' . number_format($value, 0, ',', '.'),
            set: fn ($value) => str_replace(['Rp ', '.', ','], '', $value)
        );
    }

    public function subKegiatan()
    {
        return $this->belongsTo(SubKegiatan::class);
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
