<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Realisasi extends Model
{
    protected $fillable = [
        'anggaran_id',
        'nilai_realisasi',
        'tahun'
    ];

    public function anggaran()
    {
        return $this->belongsTo(Anggaran::class);
    }
}
