<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table = 'kegiatans';
    protected $fillable = ['kode', 'nama', 'program_id'];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function rincianAnggaran()
    {
        return $this->hasMany(RincianAnggaran::class);
    }

    public static function insertIfNotDuplicate($kode, $nama, $programId)
    {
        return self::firstOrCreate(['kode' => $kode, 'nama' => $nama, 'program_id' => $programId]);
    }
}
