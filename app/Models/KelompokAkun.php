<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelompokAkun extends Model
{
    protected $table = 'kelompok_akuns';
    protected $fillable = ['akun_id','kode', 'nama' ];

    public function akun()
    {
        return $this->belongsTo(Akun::class);
    }
}
