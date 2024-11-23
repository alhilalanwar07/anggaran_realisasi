<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RincianObyekAkun extends Model
{
    protected $table = 'rincian_obyek_akuns';
    protected $fillable = ['kode', 'nama', 'obyek_akun_id'];

    public function obyekAkun()
    {
        return $this->belongsTo(ObyekAkun::class);
    }

    public function subRincianObyekAkuns()
    {
        return $this->hasMany(SubRincianObyekAkun::class);
    }

}
