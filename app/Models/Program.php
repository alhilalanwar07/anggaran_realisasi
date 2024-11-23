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

}
