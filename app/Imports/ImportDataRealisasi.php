<?php

namespace App\Imports;

use App\Models\Anggaran;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use App\Models\Realisasi;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ImportDataRealisasi implements ToCollection, WithHeadingRow, WithChunkReading
{
    public function collection(Collection $rows)
    {
        \DB::transaction(function() use ($rows) {
            foreach ($rows as $row) {
                // kode_urusan	nama_urusan	kode_urusan_pelaksana	nama_urusan_pelaksana	kode_skpd	nama_skpd	kode_sub_skpd	nama_sub_skpd	kode_program	nama_program	kode_kegiatan	nama_kegiatan	kode_sub_kegiatan	nama_sub_kegiatan	kode_akun	nama_akun	kode_akun_kelompok	nama_akun_kelompok	kode_akun_jenis	nama_akun_jenis	kode_akun_obyek	nama_akun_obyek	kode_akun_rincian_obyek	nama_akun_rincian_obyek	kode_akun_sub_rincian_obyek	nama_akun_sub_rincian_obyek	nilai_realisasi	tahun
                // 'anggaran_id','nilai_realisasi','tahun','kode'

                // $kode = $row['kode_urusan'] . $row['kode_urusan_pelaksana'] . $row['kode_skpd'] . $row['kode_sub_skpd'] . $row['kode_program'] . $row['kode_kegiatan'] . $row['kode_sub_kegiatan'] . $row['kode_akun'] . $row['kode_akun_kelompok'] . $row['kode_akun_jenis'] . $row['kode_akun_obyek'] . $row['kode_akun_rincian_obyek'] . $row['kode_akun_sub_rincian_obyek'];

                // if ($row->contains('#N/A')) {
                //     continue;
                // }

                $kode = $row['kode_urusan'] . '.' . $row['kode_urusan_pelaksana'] . '.' . $row['kode_skpd'] . '.' . $row['kode_sub_skpd'] . '.' . $row['kode_program'] . '.' . $row['kode_kegiatan'] . '.' . $row['kode_sub_kegiatan'] . '.' . $row['kode_akun'] . '.' . $row['kode_akun_kelompok'] . '.' . $row['kode_akun_jenis'] . '.' . $row['kode_akun_obyek'] . '.' . $row['kode_akun_rincian_obyek'] . '.' . $row['kode_akun_sub_rincian_obyek'];

                // if()
                $anggaran = Anggaran::where('kode', $kode)->where('tahun', $row['tahun'])->first();
                $anggaran_id = $anggaran ? $anggaran->id : null;

                //jika nilai_realisasi = -, maka beri nilai 0
                if ($row['nilai_realisasi'] == '-') {
                    $row['nilai_realisasi'] = 0;
                }

                Realisasi::create([
                    'anggaran_id' => $anggaran_id,
                    'nilai_realisasi' => $row['nilai_realisasi'],
                    'tahun' => $row['tahun'],
                    'kode' => $kode,
                ]);
            }
        });
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
