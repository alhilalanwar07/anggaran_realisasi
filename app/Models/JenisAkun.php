<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisAkun extends Model
{
    protected $table = 'jenis_akuns';
    protected $fillable = ['kode', 'nama', 'kelompok_akun_id'];

    public function kelompokAkun()
    {
        return $this->belongsTo(KelompokAkun::class);
    }

    public function obyekAkuns()
    {
        return $this->hasMany(ObyekAkun::class);
    }
}
