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
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;


class ImportData implements ToCollection, WithHeadingRow, WithChunkReading
{
    public function collection(Collection $rows)
    {
        \DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $fields = [
                    'kode_urusan',
                    'nama_urusan',
                    'kode_urusan_pelaksana',
                    'nama_urusan_pelaksana',
                    'kode_skpd',
                    'nama_skpd',
                    'kode_sub_skpd',
                    'nama_sub_skpd',
                    'kode_program',
                    'nama_program',
                    'kode_kegiatan',
                    'nama_kegiatan',
                    'kode_sub_kegiatan',
                    'nama_sub_kegiatan',
                    'kode_akun',
                    'nama_akun',
                    'kode_akun_kelompok',
                    'nama_akun_kelompok',
                    'kode_akun_jenis',
                    'nama_akun_jenis',
                    'kode_akun_obyek',
                    'nama_akun_obyek',
                    'kode_akun_rincian_obyek',
                    'nama_akun_rincian_obyek',
                ];

                foreach ($fields as $field) {
                    if (empty($row[$field])) {
                        $row[$field] = '#N/A';
                    }
                }

                // jika kode_akun_sub_rincian_obyek kosong, maka skip
                if (empty($row['kode_akun_sub_rincian_obyek'])) {
                    continue;
                }

                // Urusan
                if ($row['kode_urusan'] !== '#N/A' && $row['nama_urusan'] !== '#N/A') {
                    $urusan = Urusan::insertIfNotDuplicate($row['kode_urusan'], $row['nama_urusan']);
                }

                // Urusan Pelaksana
                $urusanPelaksana = null;
                if (!empty($row['kode_urusan_pelaksana']) && !empty($row['nama_urusan_pelaksana'])) {
                    $urusanPelaksana = UrusanPelaksana::insertIfNotDuplicate(
                        $row['kode_urusan_pelaksana'],
                        $row['nama_urusan_pelaksana'],
                        $urusan->id
                    );
                }

                // SKPD
                $skpd = null;
                if (!empty($row['kode_skpd']) && !empty($row['nama_skpd'])) {
                    $skpd = Skpd::insertIfNotDuplicate(
                        $row['kode_skpd'],
                        $row['nama_skpd'],
                        $urusanPelaksana->id ?? null
                    );
                }

                // Sub SKPD
                $subSkpd = null;
                if (!empty($row['kode_sub_skpd']) && !empty($row['nama_sub_skpd'])) {
                    $subSkpd = SubSkpd::insertIfNotDuplicate(
                        $row['kode_sub_skpd'],
                        $row['nama_sub_skpd'],
                        $skpd->id ?? null
                    );
                }

                // Program
                $program = null;
                if (!empty($row['kode_program']) && !empty($row['nama_program'])) {
                    $program = Program::insertIfNotDuplicate(
                        $row['kode_program'],
                        $row['nama_program'],
                        $subSkpd->id ?? null
                    );
                }

                // Kegiatan
                $kegiatan = null;
                if (!empty($row['kode_kegiatan']) && !empty($row['nama_kegiatan'])) {
                    $kegiatan = Kegiatan::insertIfNotDuplicate(
                        $row['kode_kegiatan'],
                        $row['nama_kegiatan'],
                        $program->id ?? null
                    );
                }

                // Sub Kegiatan
                $subKegiatan = null;
                if (!empty($row['kode_sub_kegiatan']) && !empty($row['nama_sub_kegiatan'])) {
                    $subKegiatan = SubKegiatan::insertIfNotDuplicate(
                        $row['kode_sub_kegiatan'],
                        $row['nama_sub_kegiatan'],
                        $kegiatan->id ?? null
                    );
                }

                // Akun
                $akun = null;
                if (!empty($row['kode_akun']) && !empty($row['nama_akun'])) {
                    $akun = Akun::insertIfNotDuplicate(
                        $row['kode_akun'],
                        $row['nama_akun']
                    );
                }

                // Kelompok Akun
                $kelompokAkun = null;
                if (!empty($row['kode_akun_kelompok']) && !empty($row['nama_akun_kelompok'])) {
                    $kelompokAkun = KelompokAkun::insertIfNotDuplicate(
                        $row['kode_akun_kelompok'],
                        $row['nama_akun_kelompok'],
                        $akun->id ?? null
                    );
                }

                // Jenis Akun
                $jenisAkun = null;
                if (!empty($row['kode_akun_jenis']) && !empty($row['nama_akun_jenis'])) {
                    $jenisAkun = JenisAkun::insertIfNotDuplicate(
                        $row['kode_akun_jenis'],
                        $row['nama_akun_jenis'],
                        $kelompokAkun->id ?? null
                    );
                }

                // Obyek Akun
                $obyekAkun = null;
                if (!empty($row['kode_akun_obyek']) && !empty($row['nama_akun_obyek'])) {
                    $obyekAkun = ObyekAkun::insertIfNotDuplicate(
                        $row['kode_akun_obyek'],
                        $row['nama_akun_obyek'],
                        $jenisAkun->id ?? null
                    );
                }

                // Rincian Obyek Akun
                $rincianObyekAkun = null;
                if (!empty($row['kode_akun_rincian_obyek']) && !empty($row['nama_akun_rincian_obyek'])) {
                    $rincianObyekAkun = RincianObyekAkun::insertIfNotDuplicate(
                        $row['kode_akun_rincian_obyek'],
                        $row['nama_akun_rincian_obyek'],
                        $obyekAkun->id ?? null
                    );
                }

                // Sub Rincian Obyek Akun
                $subRincianObyekAkun = null;
                if (!empty($row['kode_akun_sub_rincian_obyek']) && !empty($row['nama_akun_sub_rincian_obyek'])) {
                    $subRincianObyekAkun = SubRincianObyekAkun::insertIfNotDuplicate(
                        $row['kode_akun_sub_rincian_obyek'],
                        $row['nama_akun_sub_rincian_obyek'],
                        $rincianObyekAkun->id ?? null
                    );
                }

                // Dapatkan kode gabungan
                $kode = implode('.', array_filter([
                    $row['kode_urusan'],
                    $row['kode_urusan_pelaksana'],
                    $row['kode_skpd'],
                    $row['kode_sub_skpd'],
                    $row['kode_program'],
                    $row['kode_kegiatan'],
                    $row['kode_sub_kegiatan'],
                    $row['kode_akun'],
                    $row['kode_akun_kelompok'],
                    $row['kode_akun_jenis'],
                    $row['kode_akun_obyek'],
                    $row['kode_akun_rincian_obyek'],
                    $row['kode_akun_sub_rincian_obyek'],
                ]));

                // Simpan Anggaran
                // Validasi kode tidak boleh ada yang sama sebelum menyimpan ke Anggaran
                $existingAnggaran = Anggaran::where('sub_kegiatan_id', $subKegiatan->id ?? null)
                    ->where('sub_rincian_obyek_akun_id', $subRincianObyekAkun->id ?? null)
                    ->where('tahun', $row['tahun'] ?? '#N/A')
                    ->where('kode', $kode)
                    ->first();

                // row['nilai_anggaran'] di excel berbentuk desimal dengan 2 angka dibelakang koma


                if (!$existingAnggaran) {
                    Anggaran::updateOrCreate(
                        [
                            'sub_kegiatan_id' => $subKegiatan->id ?? null,
                            'sub_rincian_obyek_akun_id' => $subRincianObyekAkun->id ?? null,
                            'tahun' => $row['tahun'] ?? '#N/A',
                            'kode' => $kode,
                        ],
                        [
                            'nilai_anggaran' => round(floatval($row['nilai_anggaran']), 2),
                        ]
                    );
                }
            }
        });
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
