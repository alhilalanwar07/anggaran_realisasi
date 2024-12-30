<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKegiatan extends Model
{
    protected $table = 'sub_kegiatans';
    protected $fillable = ['kode', 'nama', 'kegiatan_id'];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public static function insertIfNotDuplicate($kode, $nama, $kegiatanId)
    {
        return self::firstOrCreate(['kode' => $kode, 'nama' => $nama, 'kegiatan_id' => $kegiatanId]);
    }

    // ke anggaran
    public function anggaran()
    {
        return $this->hasMany(Anggaran::class);
    }   
}
