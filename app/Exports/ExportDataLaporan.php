<?php

namespace App\Exports;

use App\Models\Realisasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportDataLaporan implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $realisasi;

    public function __construct($realisasi)
    {
        $this->realisasi = $realisasi;
        // dd($realisasi);
    }

    public function collection()
    {
        return $this->realisasi;
    }

    public function map($realisasi): array
    {
        // Set anggaran_id to #N/A for records with null anggaran_id
        if (is_null($realisasi->anggaran_id)) {
            $realisasi->anggaran_id = "#N/A";
        }

        $kode_parts = $realisasi->anggaran === null ? explode('.', $realisasi->kode) : null;
        
        return [
            $realisasi->anggaran ? $realisasi->anggaran->tahun : null,
            // Urusan
            $realisasi->anggaran ? $realisasi->anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->urusanPelaksana->urusan->kode : ($kode_parts[0] ?? null),
            $realisasi->anggaran ? $realisasi->anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->urusanPelaksana->urusan->nama : null,
            // Urusan Pelaksana
            $realisasi->anggaran ? $realisasi->anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->urusanPelaksana->kode : ($kode_parts[1] ?? null),
            $realisasi->anggaran ? $realisasi->anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->urusanPelaksana->nama : null,
            // SKPD
            $realisasi->anggaran ? $realisasi->anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->kode : ($kode_parts[2] ?? null),
            $realisasi->anggaran ? $realisasi->anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->nama : null,
            // Sub SKPD
            $realisasi->anggaran ? $realisasi->anggaran->subKegiatan->kegiatan->program->subSkpd->kode : ($kode_parts[3] ?? null),
            $realisasi->anggaran ? $realisasi->anggaran->subKegiatan->kegiatan->program->subSkpd->nama : null,
            // Program
            $realisasi->anggaran ? $realisasi->anggaran->subKegiatan->kegiatan->program->kode : ($kode_parts[4] ?? null),
            $realisasi->anggaran ? $realisasi->anggaran->subKegiatan->kegiatan->program->nama : null,
            // Kegiatan
            $realisasi->anggaran ? $realisasi->anggaran->subKegiatan->kegiatan->kode : ($kode_parts[5] ?? null),
            $realisasi->anggaran ? $realisasi->anggaran->subKegiatan->kegiatan->nama : null,
            // Sub Kegiatan
            $realisasi->anggaran ? $realisasi->anggaran->subKegiatan->kode : ($kode_parts[6] ?? null),
            $realisasi->anggaran ? $realisasi->anggaran->subKegiatan->nama : null,
            // Akun
            $realisasi->anggaran ? $realisasi->anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun->kelompokAkun->akun->kode : ($kode_parts[7] ?? null),
            $realisasi->anggaran ? $realisasi->anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun->kelompokAkun->akun->nama : null,
            // Kelompok Akun
            $realisasi->anggaran ? $realisasi->anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun->kelompokAkun->kode : ($kode_parts[8] ?? null),
            $realisasi->anggaran ? $realisasi->anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun->kelompokAkun->nama : null,
            // Jenis Akun
            $realisasi->anggaran ? $realisasi->anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun->kode : ($kode_parts[9] ?? null),
            $realisasi->anggaran ? $realisasi->anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun->nama : null,
            // Obyek Akun
            $realisasi->anggaran ? $realisasi->anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->kode : ($kode_parts[10] ?? null),
            $realisasi->anggaran ? $realisasi->anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->nama : null,
            // Rincian Obyek Akun
            $realisasi->anggaran ? $realisasi->anggaran->subRincianObyekAkun->rincianObyekAkun->kode : ($kode_parts[11] ?? null),
            $realisasi->anggaran ? $realisasi->anggaran->subRincianObyekAkun->rincianObyekAkun->nama : null,
            // Sub Rincian Obyek Akun
            $realisasi->anggaran ? $realisasi->anggaran->subRincianObyekAkun->kode : ($kode_parts[12] ?? null),
            $realisasi->anggaran ? $realisasi->anggaran->subRincianObyekAkun->nama : null,
            // Nilai Anggaran
            $realisasi->anggaran ? $realisasi->anggaran->nilai_anggaran : 0,
            // Nilai dan Tahun
            $realisasi->nilai_realisasi,
        ];
    }

    public function headings(): array
    {
        return [
            'Tahun',
            'Kode Urusan',
            'Nama Urusan',
            'Kode Urusan Pelaksana',
            'Nama Urusan Pelaksana',
            'Kode SKPD',
            'Nama SKPD',
            'Kode Sub SKPD',
            'Nama Sub SKPD',
            'Kode Program',
            'Nama Program',
            'Kode Kegiatan',
            'Nama Kegiatan',
            'Kode Sub Kegiatan',
            'Nama Sub Kegiatan',
            'Kode Akun',
            'Nama Akun',
            'Kode Kelompok Akun',
            'Nama Kelompok Akun',
            'Kode Jenis Akun',
            'Nama Jenis Akun',
            'Kode Obyek Akun',
            'Nama Obyek Akun',
            'Kode Rincian Obyek Akun',
            'Nama Rincian Obyek Akun',
            'Kode Sub Rincian Obyek Akun',
            'Nama Sub Rincian Obyek Akun',
            'Nilai Anggaran',
            'Nilai Realisasi',
        ];
    }
}
