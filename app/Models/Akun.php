<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Akun extends Model
{
    protected $table = 'akuns';
    protected $fillable = ['kode', 'nama'];

    public function kelompokAkun()
    {
        return $this->hasMany(KelompokAkun::class);
    }
}
