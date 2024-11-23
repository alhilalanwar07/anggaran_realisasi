<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubSkpd extends Model
{
    protected $table = 'sub_skpds';
    protected $fillable = ['kode', 'nama', 'skpd_id'];

    public function skpd(): BelongsTo
    {
        return $this->belongsTo(Skpd::class);
    }

    public static function insertIfNotDuplicate($kode, $nama, $skpdId)
    {
        return self::firstOrCreate(['kode' => $kode, 'nama' => $nama, 'skpd_id' => $skpdId]);
    }
}
