<?php

namespace App\Models;

use App\Models\UrusanPelaksana;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Urusan extends Model
{
    protected $table = 'urusans';
    protected $fillable = ['kode', 'nama'];

    public function urusanPelaksanas(): HasMany
    {
        return $this->hasMany(UrusanPelaksana::class);
    }

    public static function insertIfNotDuplicate($kode, $nama)
    {
        return self::firstOrCreate(['kode' => $kode, 'nama' => $nama]);
    }

}
