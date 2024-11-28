<?php

namespace App\Imports;

use App\Models\Urusan;
use App\Models\UrusanPelaksana;
use App\Models\Skpd;
use App\Models\SubSkpd;
use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\Akun;
use App\Models\KelompokAkun;
use App\Models\JenisAkun;
use App\Models\ObyekAkun;
use App\Models\RincianObyekAkun;
use App\Models\SubRincianObyekAkun;
use App\Models\Anggaran;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;


class ImportData implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        \DB::transaction(function() use ($rows) {
            foreach ($rows as $row) {
                // Validate required fields
                if (empty($row['kode_urusan']) || empty($row['nama_urusan'])) {
                    continue; // Skip this row if required fields are missing
                }

                // Urusan
                $urusan = Urusan::insertIfNotDuplicate($row['kode_urusan'], $row['nama_urusan']);

                // Urusan Pelaksana
                if (!empty($row['kode_urusan_pelaksana']) && !empty($row['nama_urusan_pelaksana'])) {
                    $urusanPelaksana = UrusanPelaksana::insertIfNotDuplicate($row['kode_urusan_pelaksana'], $row['nama_urusan_pelaksana'], $urusan->id);
                }

                // SKPD
                if (!empty($row['kode_skpd']) && !empty($row['nama_skpd'])) {
                    $skpd = Skpd::insertIfNotDuplicate($row['kode_skpd'], $row['nama_skpd'], $urusanPelaksana->id);
                }

                // Sub SKPD
                if (!empty($row['kode_sub_skpd']) && !empty($row['nama_sub_skpd'])) {
                    $subSkpd = SubSkpd::insertIfNotDuplicate($row['kode_sub_skpd'], $row['nama_sub_skpd'], $skpd->id);
                }

                // Program
                if (!empty($row['kode_program']) && !empty($row['nama_program'])) {
                    $program = Program::insertIfNotDuplicate($row['kode_program'], $row['nama_program'], $subSkpd->id);
                }

                // Kegiatan
                if (!empty($row['kode_kegiatan']) && !empty($row['nama_kegiatan'])) {
                    $kegiatan = Kegiatan::insertIfNotDuplicate($row['kode_kegiatan'], $row['nama_kegiatan'], $program->id);
                }

                // Sub Kegiatan
                if (!empty($row['kode_sub_kegiatan']) && !empty($row['nama_sub_kegiatan'])) {
                    $subKegiatan = SubKegiatan::insertIfNotDuplicate($row['kode_sub_kegiatan'], $row['nama_sub_kegiatan'], $kegiatan->id);
                }

                // Akun
                if (!empty($row['kode_akun']) && !empty($row['nama_akun'])) {
                    $akun = Akun::insertIfNotDuplicate($row['kode_akun'], $row['nama_akun']);
                }

                // Kelompok Akun
                if (!empty($row['kode_akun_kelompok']) && !empty($row['nama_akun_kelompok'])) {
                    $kelompokAkun = KelompokAkun::insertIfNotDuplicate($row['kode_akun_kelompok'], $row['nama_akun_kelompok'], $akun->id);
                }

                // Jenis Akun
                if (!empty($row['kode_akun_jenis']) && !empty($row['nama_akun_jenis'])) {
                    $jenisAkun = JenisAkun::insertIfNotDuplicate($row['kode_akun_jenis'], $row['nama_akun_jenis'], $kelompokAkun->id);
                }

                // Obyek Akun
                if (!empty($row['kode_akun_obyek']) && !empty($row['nama_akun_obyek'])) {
                    $obyekAkun = ObyekAkun::insertIfNotDuplicate($row['kode_akun_obyek'], $row['nama_akun_obyek'], $jenisAkun->id);
                }

                // Rincian Obyek Akun
                if (!empty($row['kode_akun_rincian_obyek']) && !empty($row['nama_akun_rincian_obyek'])) {
                    $rincianObyekAkun = RincianObyekAkun::insertIfNotDuplicate($row['kode_akun_rincian_obyek'], $row['nama_akun_rincian_obyek'], $obyekAkun->id);
                }

                // Sub Rincian Obyek Akun
                if (!empty($row['kode_akun_sub_rincian_obyek']) && !empty($row['nama_akun_sub_rincian_obyek'])) {
                    $subRincianObyekAkun = SubRincianObyekAkun::insertIfNotDuplicate($row['kode_akun_sub_rincian_obyek'], $row['nama_akun_sub_rincian_obyek'], $rincianObyekAkun->id);
                }

                // Anggaran
                if (!empty($row['nilai_anggaran']) && !empty($row['nilai_realisasi']) && !empty($row['tahun'])) {
                    Anggaran::updateOrCreate(
                        [
                            'sub_kegiatan_id' => $subKegiatan->id,
                            'sub_rincian_obyek_akun_id' => $subRincianObyekAkun->id
                        ],
                        [
                            'nilai_anggaran' => $row['nilai_anggaran'],
                            // 'nilai_realisasi' => 0,
                            'tahun' => $row['tahun']
                        ]
                    );
                }
            }
        });
    }
}
