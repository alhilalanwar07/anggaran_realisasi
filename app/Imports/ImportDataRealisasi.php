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
                // Validate required fields
                if (empty($row['kode_urusan']) || empty($row['nama_urusan'])) {
                    continue; // Skip this row if required fields are missing
                }

                //

                // Find the existing Anggaran record
                $anggaran = Anggaran::where();

                // Update nilai_realisasi if Anggaran record exists
                if ($anggaran) {
                    $anggaran->update([
                        'nilai_realisasi' => $row['nilai_realisasi']
                    ]);
                }
            }
        });
    }
}
