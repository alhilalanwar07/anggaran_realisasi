<?php

namespace App\Imports;

use App\Models\Anggaran;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class ImportDataRealisasi implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        \DB::transaction(function() use ($rows) {
            foreach ($rows as $row) {
                // Find anggaran by sub_kegiatan_id and sub_rincian_obyek_akun_id
                $anggaran = Anggaran::where('sub_kegiatan_id', $row['sub_kegiatan_id'])
                    ->where('sub_rincian_obyek_akun_id', $row['sub_rincian_obyek_akun_id'])
                    ->first();

                // 
            }
        });
    }
}
