<?php

namespace App\Exports;

use App\Models\Anggaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportData implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $tahun;

    public function __construct($tahun = null) 
    {
        $this->tahun = $tahun;
    }

    public function collection()
    {
        return Anggaran::with([
            'subKegiatan.kegiatan.program.subSkpd.skpd.urusanPelaksana.urusan',
            'subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun.akun'
        ])
        ->when($this->tahun, function($query) {
            return $query->where('tahun', $this->tahun);
        })
        ->get();
    }

    public function map($anggaran): array
    {
        return [
            $anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->urusanPelaksana->urusan->kode,
            $anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->urusanPelaksana->urusan->nama,
            $anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->urusanPelaksana->kode,
            $anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->urusanPelaksana->nama,
            $anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->kode,
            $anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->nama,
            $anggaran->subKegiatan->kegiatan->program->subSkpd->kode,
            $anggaran->subKegiatan->kegiatan->program->subSkpd->nama,
            $anggaran->subKegiatan->kegiatan->program->kode,
            $anggaran->subKegiatan->kegiatan->program->nama,
            $anggaran->subKegiatan->kegiatan->kode,
            $anggaran->subKegiatan->kegiatan->nama,
            $anggaran->subKegiatan->kode,
            $anggaran->subKegiatan->nama,
            $anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun->kelompokAkun->akun->kode,
            $anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun->kelompokAkun->akun->nama,
            $anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun->kelompokAkun->kode,
            $anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun->kelompokAkun->nama,
            $anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun->kode,
            $anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun->nama,
            $anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->kode,
            $anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->nama,
            $anggaran->subRincianObyekAkun->rincianObyekAkun->kode,
            $anggaran->subRincianObyekAkun->rincianObyekAkun->nama,
            $anggaran->subRincianObyekAkun->kode,
            $anggaran->subRincianObyekAkun->nama,
            $anggaran->nilai_anggaran,
            $anggaran->tahun
        ];
    }

    public function headings(): array
    {
        return [
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
            'kode_akun_sub_rincian_obyek',
            'nama_akun_sub_rincian_obyek',
            'nilai_anggaran',
            'tahun'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}