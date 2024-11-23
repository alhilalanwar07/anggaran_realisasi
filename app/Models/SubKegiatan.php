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
}
