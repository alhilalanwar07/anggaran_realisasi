<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'programs';
    protected $fillable = ['kode', 'nama', 'sub_skpd_id'];

    public function subSkpd()
    {
        return $this->belongsTo(SubSkpd::class);
    }

    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class);
    }

    public static function insertIfNotDuplicate($kode, $nama, $subSkpdId)
    {
        return self::firstOrCreate(['kode' => $kode, 'nama' => $nama, 'sub_skpd_id' => $subSkpdId]);
    }

}
