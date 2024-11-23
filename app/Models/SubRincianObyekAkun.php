<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubRincianObyekAkun extends Model
{
    protected $table = 'sub_rincian_obyek_akuns';
    protected $fillable = ['kode', 'nama', 'rincian_obyek_akun_id'];

    public function rincianObyekAkun()
    {
        return $this->belongsTo(RincianObyekAkun::class);
    }

    public static function insertIfNotDuplicate($kode, $nama, $rincianObyekAkunId)
    {
        return self::firstOrCreate(['kode' => $kode, 'nama' => $nama, 'rincian_obyek_akun_id' => $rincianObyekAkunId]);
    }

    
}
