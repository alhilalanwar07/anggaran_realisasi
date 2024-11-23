<?php

namespace App\Models;

use App\Models\JenisAkun;
use Illuminate\Database\Eloquent\Model;

class ObyekAkun extends Model
{
    protected $table = 'obyek_akuns';
    protected $fillable = ['kode', 'nama', 'jenis_akun_id'];

    public function jenisAkun()
    {
        return $this->belongsTo(JenisAkun::class);
    }

    public function rincianObyekAkuns()
    {
        return $this->hasMany(RincianObyekAkun::class);
    }

    public static function insertIfNotDuplicate($kode, $nama, $jenisAkunId)
    {
        return self::firstOrCreate(['kode' => $kode, 'nama' => $nama, 'jenis_akun_id' => $jenisAkunId]);
    }
}
