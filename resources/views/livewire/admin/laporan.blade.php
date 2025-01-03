<?php

use App\Exports\ExportDataLaporan;
use App\Exports\ExportDataRealisasi;
use Livewire\Volt\Component;
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
use App\Models\Realisasi;
use function Livewire\Volt\{computed, state};
use Livewire\WithPagination;
use App\Exports\RealisasiExport;
use Maatwebsite\Excel\Facades\Excel;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = "bootstrap";

    public $urusan = [];
    public $urusanPelaksana = [];
    public $skpd = [];
    public $subSkpd = [];
    public $program = [];
    public $kegiatan = [];
    public $subKegiatan = [];
    public $akun = [];
    public $kelompokAkun = [];
    public $jenisAkun = [];
    public $obyekAkun = [];
    public $rincianObyekAkun = [];
    public $subRincianObyekAkun = [];
    public $tahun;
    public $realisasi = [];

    public $urusan_id;
    public $urusan_pelaksana_id;
    public $skpd_id;
    public $sub_skpd_id;
    public $program_id;
    public $kegiatan_id;
    public $sub_kegiatan_id;
    public $akun_id;
    public $kelompok_akun_id;
    public $jenis_akun_id;
    public $obyek_akun_id;
    public $rincian_obyek_akun_id;
    public $sub_rincian_obyek_akun_id;
    public $anggaran_id;

    public $realisasiAda = false;
    public $grafikAda = false;

    public $colors = [], $labels = [], $data = []; // untuk chart

    
    public $label_chart_tahunan = [];
    public $data_chart_realisasi_tahunan = [];
    public $data_chart_anggaran_tahunan = [];
    public $colors_chart_tahunan = [];


    public function with(): array
    {
        $chartData = $this->chartPerTahun();
        return [
            "urusanPelaksana" => $this->getDataUrusanPelaksana($this->urusan_id),
            "skpd" => $this->getDataSkpd($this->urusan_pelaksana_id),
            "subSkpd" => $this->getDataSubSkpd($this->skpd_id),
            "program" => $this->getDataProgram($this->sub_skpd_id),
            "kegiatan" => $this->getDataKegiatan($this->program_id),
            "subKegiatan" => $this->getDataSubKegiatan($this->kegiatan_id),
            "kelompokAkun" => $this->getDataKelompokAkun($this->akun_id),
            "jenisAkun" => $this->getDataJenisAkun($this->kelompok_akun_id),
            "obyekAkun" => $this->getDataObyekAkun($this->jenis_akun_id),
            "rincianObyekAkun" => $this->getDataRincianObyekAkun($this->obyek_akun_id),
            "subRincianObyekAkun" => $this->getDataSubRincianObyekAkun($this->rincian_obyek_akun_id),
            // 'tahun' => $this->getDataTahun(),
            // 'realisasi' => $this->getDataRealisasi(),
            'label_chart_tahunan' => $chartData['label_chart_tahunan'],
            'data_chart_realisasi_tahunan' => $chartData['data_chart_realisasi_tahunan'],
            'data_chart_anggaran_tahunan' => $chartData['data_chart_anggaran_tahunan'],
            'colors_chart_tahunan' => $chartData['colors_chart_tahunan'],
        ];
    }

    public function randomColors($count)
    {
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $colors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        }
        return $colors;
    }

    public function formatRupiah($angka)
    {
        $hasil_rupiah = "Rp " . number_format($angka, 2, ",", ".");
        return $hasil_rupiah;
    }

    public function mount()
    {
        $this->urusan = $this->getDataUrusan();
        $this->akun = $this->getDataAkun();
    }

    public function updatedUrusanId($value)
    {
        $this->urusanPelaksana = $this->getDataUrusanPelaksana($value);
        $this->reset(["urusan_pelaksana_id", "skpd_id", "sub_skpd_id", "program_id", "kegiatan_id", "sub_kegiatan_id"]);
    }

    public function updatedUrusanPelaksanaId($value)
    {
        $this->skpd = $this->getDataSkpd($value);
        $this->reset(["skpd_id", "sub_skpd_id", "program_id", "kegiatan_id", "sub_kegiatan_id"]);
    }

    public function updatedSkpdId($value)
    {
        $this->subSkpd = $this->getDataSubSkpd($value);
        $this->reset(["sub_skpd_id", "program_id", "kegiatan_id", "sub_kegiatan_id"]);
    }

    public function updatedSubSkpdId($value)
    {
        $this->program = $this->getDataProgram($value);
        $this->reset(["program_id", "kegiatan_id", "sub_kegiatan_id"]);
    }

    public function updatedProgramId($value)
    {
        $this->kegiatan = $this->getDataKegiatan($value);
        $this->reset(["kegiatan_id", "sub_kegiatan_id"]);
    }

    public function updatedKegiatanId($value)
    {
        $this->subKegiatan = $this->getDataSubKegiatan($value);
        $this->reset(["sub_kegiatan_id"]);
    }

    public function updatedAkunId($value)
    {
        $this->kelompokAkun = $this->getDataKelompokAkun($value);
        $this->reset(["kelompok_akun_id", "jenis_akun_id", "obyek_akun_id", "rincian_obyek_akun_id", "sub_rincian_obyek_akun_id"]);
    }

    public function updatedKelompokAkunId($value)
    {
        $this->jenisAkun = $this->getDataJenisAkun($value);
        $this->reset(["jenis_akun_id", "obyek_akun_id", "rincian_obyek_akun_id", "sub_rincian_obyek_akun_id"]);
    }

    public function updatedJenisAkunId($value)
    {
        $this->obyekAkun = $this->getDataObyekAkun($value);
        $this->reset(["obyek_akun_id", "rincian_obyek_akun_id", "sub_rincian_obyek_akun_id"]);
    }

    public function updatedObyekAkunId($value)
    {
        $this->rincianObyekAkun = $this->getDataRincianObyekAkun($value);
        $this->reset(["rincian_obyek_akun_id", "sub_rincian_obyek_akun_id"]);
    }

    public function updatedRincianObyekAkunId($value)
    {
        $this->subRincianObyekAkun = $this->getDataSubRincianObyekAkun($value);
        $this->reset(["sub_rincian_obyek_akun_id"]);
    }

    public function getDataUrusan()
    {
        return Urusan::orderBy("kode")->get();
    }

    public function getDataUrusanPelaksana($urusan_id)
    {
        return UrusanPelaksana::where("urusan_id", $urusan_id)
            ->orderBy("kode")
            ->get();
    }

    public function getDataSkpd($urusan_pelaksana_id)
    {
        return Skpd::where("urusan_pelaksana_id", $urusan_pelaksana_id)
            ->orderBy("kode")
            ->get();
    }

    public function getDataSubSkpd($skpd_id)
    {
        return SubSkpd::where("skpd_id", $skpd_id)
            ->orderBy("kode")
            ->get();
    }

    public function getDataProgram($sub_skpd_id)
    {
        return Program::where("sub_skpd_id", $sub_skpd_id)
            ->orderBy("kode")
            ->get();
    }

    public function getDataKegiatan($program_id)
    {
        return Kegiatan::where("program_id", $program_id)
            ->orderBy("kode")
            ->get();
    }

    public function getDataSubKegiatan($kegiatan_id)
    {
        return SubKegiatan::where("kegiatan_id", $kegiatan_id)
            ->orderBy("kode")
            ->get();
    }

    public function getDataAkun()
    {
        return Akun::orderBy("kode")->get();
    }

    public function getDataKelompokAkun($akun_id)
    {
        return KelompokAkun::where("akun_id", $akun_id)
            ->orderBy("kode")
            ->get();
    }

    public function getDataJenisAkun($kelompok_akun_id)
    {
        return JenisAkun::where("kelompok_akun_id", $kelompok_akun_id)
            ->orderBy("kode")
            ->get();
    }

    public function getDataObyekAkun($jenis_akun_id)
    {
        return ObyekAkun::where("jenis_akun_id", $jenis_akun_id)
            ->orderBy("kode")
            ->get();
    }

    public function getDataRincianObyekAkun($obyek_akun_id)
    {
        return RincianObyekAkun::where("obyek_akun_id", $obyek_akun_id)
            ->orderBy("kode")
            ->get();
    }

    public function getDataSubRincianObyekAkun($rincian_obyek_akun_id)
    {
        return SubRincianObyekAkun::where("rincian_obyek_akun_id", $rincian_obyek_akun_id)
            ->orderBy("kode")
            ->get();
    }

    public function getDataRealisasi()
    {
        // Proteksi data apa yang terisi dari form
        $this->validate([
            "urusan_id" => "nullable|exists:App\Models\Urusan,id",
            "urusan_pelaksana_id" => "nullable|exists:App\Models\UrusanPelaksana,id",
            "skpd_id" => "nullable|exists:App\Models\Skpd,id",
            "sub_skpd_id" => "nullable|exists:App\Models\SubSkpd,id",
            "program_id" => "nullable|exists:App\Models\Program,id",
            "kegiatan_id" => "nullable|exists:App\Models\Kegiatan,id",
            "sub_kegiatan_id" => "nullable|exists:App\Models\SubKegiatan,id",
            "akun_id" => "nullable|exists:App\Models\Akun,id",
            "kelompok_akun_id" => "nullable|exists:App\Models\KelompokAkun,id",
            "jenis_akun_id" => "nullable|exists:App\Models\JenisAkun,id",
            "obyek_akun_id" => "nullable|exists:App\Models\ObyekAkun,id",
            "rincian_obyek_akun_id" => "nullable|exists:App\Models\RincianObyekAkun,id",
            "sub_rincian_obyek_akun_id" => "nullable|exists:App\Models\SubRincianObyekAkun,id",
            "tahun" => "nullable|integer",
        ]);

        $query = Realisasi::query();

        $filters = [
            "urusan_id" => "anggaran.subKegiatan.kegiatan.program.subSkpd.skpd.urusanPelaksana.urusan",
            "urusan_pelaksana_id" => "anggaran.subKegiatan.kegiatan.program.subSkpd.skpd.urusanPelaksana",
            "skpd_id" => "anggaran.subKegiatan.kegiatan.program.subSkpd.skpd",
            "sub_skpd_id" => "anggaran.subKegiatan.kegiatan.program.subSkpd",
            "program_id" => "anggaran.subKegiatan.kegiatan.program",
            "kegiatan_id" => "anggaran.subKegiatan.kegiatan",
            "sub_kegiatan_id" => "anggaran.subKegiatan",
            "akun_id" => "anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun.akun",
            "kelompok_akun_id" => "anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun",
            "jenis_akun_id" => "anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun",
            "obyek_akun_id" => "anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun",
            "rincian_obyek_akun_id" => "anggaran.subRincianObyekAkun.rincianObyekAkun",
            "sub_rincian_obyek_akun_id" => "anggaran.subRincianObyekAkun",
        ];

        $hasFilters = false;

        foreach ($filters as $key => $relation) {
            if ($this->$key) {
                $hasFilters = true;
                $query->whereHas($relation, function ($q) use ($key) {
                    $q->where("id", $this->$key);
                });
            }
        }

        if ($this->tahun) {
            $hasFilters = true;
            $query->where("tahun", $this->tahun);
        }

        // Handle case where anggaran_id is null
        if ($hasFilters) {
            $query->orWhereNull("anggaran_id")->where("tahun", $this->tahun);
        } else {
            // jika tidak memilih apa-apa
            $realisasi = Realisasi::all();
        }

        $realisasi = $query;

        // Set anggaran_id to #N/A for records with null anggaran_id
        // foreach ($realisasi as $item) {
        //     if (is_null($item->anggaran_id)) {
        //         $item->anggaran_id = "#N/A";
        //     }
        // }

        return $realisasi;
    }

    public function tampilkanRealisasi()
    {
        $this->realisasi = $this->getDataRealisasi()->get();

        if ($this->realisasi->isEmpty()) {
            $this->dispatch('tambahAlertToast', detail: [
                'type' => 'warning',
                'title' => 'Data Kosong',
                'message' => 'Harap pilih kembali data yang akan ditampilkan',
            ]);
            return;
        }

        $this->realisasiAda = true;

        // dd($this->realisasi);
    }

    // download excel
    public function downloadExcel()
    {
        // bedasarkan get data realisasi
        $this->realisasi = $this->getDataRealisasi()->get();

        // validasi jika tidak ada data
        try {
            if ($this->realisasi->isEmpty()) {
                $this->dispatch('tambahAlertToast', detail: [
                    'type' => 'warning',
                    'title' => 'Data Kosong',
                    'message' => 'Harap pilih kembali data yang akan diunduh',
                ]);
                return;
            }
            $this->dispatch('fileDownloaded');
            return Excel::download(new ExportDataLaporan($this->realisasi), 'realisasi-' . date('Y-m-d_H-i-s') . '.xlsx');
        } catch (\Exception $e) {
            $this->dispatch('tambahAlertToast', detail: [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Terjadi kesalahan saat mengunduh file: ' . $e->getMessage(),
            ]);
        }
    }

    public $label_chart_akun = [], $data_chart_realisasi_akun = [], $data_chart_anggaran_akun = [], $colors_chart_realisasi_akun = [], $colors_chart_anggaran_akun = [];

    private function groupData($groupBy)
    {
        $grouped = $this->realisasi->groupBy($groupBy);
        return [
            'labels' => $grouped->keys(),
            'data' => $grouped->map(function ($group) {
                return $group->sum('rawNilaiRealisasi');
            })->values(),
            'data_chart_anggaran_akun' => $grouped->map(function ($group) {
                return $group->sum('anggaran.rawNilaiAnggaran');
            })->values(),
            'data_chart_realisasi_akun' => $grouped->map(function ($group) {
                return $group->sum('rawNilaiRealisasi');
            })->values(),
            'colors' => $this->randomColors($grouped->count()),
            'colors_chart_anggaran_akun' => $this->randomColors($grouped->count()),
            'colors_chart_realisasi_akun' => $this->randomColors($grouped->count()),
        ];
    }

    public function tampilkanGrafik()
    {
        $this->realisasi = $this->getDataRealisasi()->get();

        if ($this->realisasi->isEmpty()) {
            $this->dispatch('tambahAlertToast', detail: [
                'type' => 'warning',
                'title' => 'Data Kosong',
                'message' => 'Harap pilih kembali data yang akan ditampilkan',
            ]);
            return;
        }

        $this->grafikAda = true;

        $groupBy = null;

        if ($this->urusan_id && !$this->urusan_pelaksana_id && !$this->skpd_id && !$this->sub_skpd_id && !$this->program_id && !$this->kegiatan_id && !$this->sub_kegiatan_id) {
            $groupBy = 'anggaran.subKegiatan.kegiatan.program.subSkpd.skpd.urusanPelaksana.nama';
        } elseif ($this->urusan_id && $this->urusan_pelaksana_id && !$this->skpd_id && !$this->sub_skpd_id && !$this->program_id && !$this->kegiatan_id && !$this->sub_kegiatan_id) {
            $groupBy = 'anggaran.subKegiatan.kegiatan.program.subSkpd.skpd.nama';
        } elseif ($this->urusan_id && $this->urusan_pelaksana_id && $this->skpd_id && !$this->sub_skpd_id && !$this->program_id && !$this->kegiatan_id && !$this->sub_kegiatan_id) {
            $groupBy = 'anggaran.subKegiatan.kegiatan.program.subSkpd.nama';
        } elseif ($this->urusan_id && $this->urusan_pelaksana_id && $this->skpd_id && $this->sub_skpd_id && !$this->program_id && !$this->kegiatan_id && !$this->sub_kegiatan_id) {
            $groupBy = 'anggaran.subKegiatan.kegiatan.program.nama';
        } elseif ($this->urusan_id && $this->urusan_pelaksana_id && $this->skpd_id && $this->sub_skpd_id && $this->program_id && !$this->kegiatan_id && !$this->sub_kegiatan_id) {
            $groupBy = 'anggaran.subKegiatan.kegiatan.nama';
        } elseif ($this->urusan_id && $this->urusan_pelaksana_id && $this->skpd_id && $this->sub_skpd_id && $this->program_id && $this->kegiatan_id && !$this->sub_kegiatan_id) {
            $groupBy = 'anggaran.subKegiatan.nama';
        } elseif ($this->urusan_id && $this->urusan_pelaksana_id && $this->skpd_id && $this->sub_skpd_id && $this->program_id && $this->kegiatan_id && $this->sub_kegiatan_id) {
            $groupBy = 'anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun.akun.nama';
        } elseif (!$this->urusan_id) {
            $groupBy = 'anggaran.subKegiatan.kegiatan.program.subSkpd.skpd.urusanPelaksana.urusan.nama';
        }

        if ($groupBy) {
            $groupedData = $this->groupData($groupBy);
            $this->labels = $groupedData['labels'];
            $this->data = $groupedData['data'];
            $this->colors = $groupedData['colors'];

            $this->dispatch('tampilkanGrafik', [
                'labels' => $this->labels,
                'data' => $this->data,
                'colors' => $this->colors,
            ]);
        }

        // akun chart
        $groupByAkun = null;

        if ($this->akun_id && !$this->kelompok_akun_id && !$this->jenis_akun_id && !$this->obyek_akun_id && !$this->rincian_obyek_akun_id && !$this->sub_rincian_obyek_akun_id) {
            $groupByAkun = 'anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun.nama';
        } elseif ($this->akun_id && $this->kelompok_akun_id && !$this->jenis_akun_id && !$this->obyek_akun_id && !$this->rincian_obyek_akun_id && !$this->sub_rincian_obyek_akun_id) {
            $groupByAkun = 'anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.nama';
        } elseif ($this->akun_id && $this->kelompok_akun_id && $this->jenis_akun_id && !$this->obyek_akun_id && !$this->rincian_obyek_akun_id && !$this->sub_rincian_obyek_akun_id) {
            $groupByAkun = 'anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.nama';
        } elseif ($this->akun_id && $this->kelompok_akun_id && $this->jenis_akun_id && $this->obyek_akun_id && !$this->rincian_obyek_akun_id && !$this->sub_rincian_obyek_akun_id) {
            $groupByAkun = 'anggaran.subRincianObyekAkun.rincianObyekAkun.nama';
        } elseif ($this->akun_id && $this->kelompok_akun_id && $this->jenis_akun_id && $this->obyek_akun_id && $this->rincian_obyek_akun_id && !$this->sub_rincian_obyek_akun_id) {
            $groupByAkun = 'anggaran.subRincianObyekAkun.nama';
        } elseif (!$this->akun_id){
            $groupByAkun = 'anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun.akun.nama';
        }

        if ($groupByAkun) {
            $groupedDataAkun = $this->groupData($groupByAkun);
            $this->label_chart_akun = $groupedDataAkun['labels'];
            $this->data_chart_realisasi_akun = $groupedDataAkun['data_chart_realisasi_akun'];
            $this->data_chart_anggaran_akun = $groupedDataAkun['data_chart_anggaran_akun'];
            $this->colors_chart_realisasi_akun = $groupedDataAkun['colors_chart_realisasi_akun'];
            $this->colors_chart_anggaran_akun = $groupedDataAkun['colors_chart_anggaran_akun'];

            $this->dispatch('tampilkanGrafikAkun', [
                'label_chart_akun' => $this->label_chart_akun,
                'data_chart_realisasi_akun' => $this->data_chart_realisasi_akun,
                'data_chart_anggaran_akun' => $this->data_chart_anggaran_akun,
                'colors_chart_realisasi_akun' => $this->colors_chart_realisasi_akun,
                'colors_chart_anggaran_akun' => $this->colors_chart_anggaran_akun,
            ]);
        }
    }

    public function reload()
    {
        // refresh page
        return redirect('/laporan');
    }


    // chart anggaran realisasi di group berdasarkan tahun (label = tahun, data = nilai realisasi dan anggaran per tahun) data tidak diambil dari fungsi getDataRealisasi()
    public function chartPerTahun()
    {
        $realisasi = Realisasi::with('anggaran')
        ->selectRaw('realisasis.tahun, sum(realisasis.nilai_realisasi) as total_realisasi, sum(anggarans.nilai_anggaran) as total_anggaran')
        ->join('anggarans', 'realisasis.anggaran_id', '=', 'anggarans.id')
        ->groupBy('realisasis.tahun')
        ->get();

        $this->label_chart_tahunan = $realisasi->pluck('tahun');
        $this->data_chart_realisasi_tahunan = $realisasi->pluck('total_realisasi');
        $this->data_chart_anggaran_tahunan = $realisasi->pluck('total_anggaran');
        $this->colors_chart_tahunan = $this->randomColors($realisasi->count());

        return [
            'label_chart_tahunan' => $this->label_chart_tahunan,
            'data_chart_realisasi_tahunan' => $this->data_chart_realisasi_tahunan,
            'data_chart_anggaran_tahunan' => $this->data_chart_anggaran_tahunan,
            'colors_chart_tahunan' => $this->colors_chart_tahunan,
        ];
    }
}; ?>

<div>
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Laporan</div>
                    <div class="card-tools">
                        {{-- cetak --}}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <!-- Urusan -->
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label>Urusan</label>
                            <select wire:model.live="urusan_id" class="form-select" wire:change="with">
                                <option value="">Pilih Urusan</option>
                                @foreach ($urusan as $u)
                                <option value="{{ $u->id }}">[{{ $u->kode }}] {{ $u->nama }}</option>
                                @endforeach
                            </select>
                            <div wire:loading wire:target="urusan_id" class="text-success">Memuat...</div>
                        </div>
                    </div>

                    <!-- Urusan Pelaksana -->
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label>Urusan Pelaksana</label>
                            <select wire:model.live="urusan_pelaksana_id" class="form-select" wire:change="with">
                                <option value="">Pilih Urusan Pelaksana</option>
                                @foreach ($urusanPelaksana as $up)
                                <option value="{{ $up->id }}">[{{ $up->kode }}] {{ $up->nama }}</option>
                                @endforeach
                            </select>
                            <div wire:loading wire:target="urusan_pelaksana_id" class="text-success">Memuat...</div>
                        </div>
                    </div>

                    <!-- SKPD -->
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label>SKPD</label>
                            <select wire:model.live="skpd_id" class="form-select" wire:change="with">
                                <option value="">Pilih SKPD</option>
                                @foreach ($skpd as $s)
                                <option value="{{ $s->id }}">[{{ $s->kode }}] {{ $s->nama }}</option>
                                @endforeach
                            </select>
                            <div wire:loading wire:target="skpd_id" class="text-success">Memuat...</div>
                        </div>
                    </div>

                    <!-- Sub SKPD -->
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label>Sub SKPD</label>
                            <select wire:model.live="sub_skpd_id" class="form-select" wire:change="with">
                                <option value="">Pilih Sub SKPD</option>
                                @foreach ($subSkpd as $ss)
                                <option value="{{ $ss->id }}">[{{ $ss->kode }}] {{ $ss->nama }}</option>
                                @endforeach
                            </select>
                            <div wire:loading wire:target="sub_skpd_id" class="text-success">Memuat...</div>
                        </div>
                    </div>

                    <!-- Program -->
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label>Program</label>
                            <select wire:model.live="program_id" class="form-select" wire:change="with">
                                <option value="">Pilih Program</option>
                                @foreach ($program as $p)
                                <option value="{{ $p->id }}">[{{ $p->kode }}] {{ $p->nama }}</option>
                                @endforeach
                            </select>
                            <div wire:loading wire:target="program_id" class="text-success">Memuat...</div>
                        </div>
                    </div>

                    <!-- Kegiatan -->
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label>Kegiatan</label>
                            <select wire:model.live="kegiatan_id" class="form-select" wire:change="with">
                                <option value="">Pilih Kegiatan</option>
                                @foreach ($kegiatan as $k)
                                <option value="{{ $k->id }}">[{{ $k->kode }}] {{ $k->nama }}</option>
                                @endforeach
                            </select>
                            <div wire:loading wire:target="kegiatan_id" class="text-success">Memuat...</div>
                        </div>
                    </div>

                    <!-- Sub Kegiatan -->
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label>Sub Kegiatan</label>
                            <select wire:model.live="sub_kegiatan_id" class="form-select" wire:change="with">
                                <option value="">Pilih Sub Kegiatan</option>
                                @foreach ($subKegiatan as $sk)
                                <option value="{{ $sk->id }}">[{{ $sk->kode }}] {{ $sk->nama }}</option>
                                @endforeach
                            </select>
                            <div wire:loading wire:target="sub_kegiatan_id" class="text-success">Memuat...</div>
                        </div>
                    </div>

                    <!-- Akun -->
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label>Akun</label>
                            <select wire:model.live="akun_id" class="form-select" wire:change="with">
                                <option value="">Pilih Akun</option>
                                @foreach ($akun as $a)
                                <option value="{{ $a->id }}">[{{ $a->kode }}] {{ $a->nama }}</option>
                                @endforeach
                            </select>
                            <div wire:loading wire:target="akun_id" class="text-success">Memuat...</div>
                        </div>
                    </div>

                    <!-- Kelompok Akun -->
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label>Kelompok Akun</label>
                            <select wire:model.live="kelompok_akun_id" class="form-select" wire:change="with">
                                <option value="">Pilih Kelompok Akun</option>
                                @foreach ($kelompokAkun as $ka)
                                <option value="{{ $ka->id }}">[{{ $ka->kode }}] {{ $ka->nama }}</option>
                                @endforeach
                            </select>
                            <div wire:loading wire:target="kelompok_akun_id" class="text-success">Memuat...</div>
                        </div>
                    </div>

                    <!-- Jenis Akun -->
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label>Jenis Akun</label>
                            <select wire:model.live="jenis_akun_id" class="form-select" wire:change="with">
                                <option value="">Pilih Jenis Akun</option>
                                @foreach ($jenisAkun as $ja)
                                <option value="{{ $ja->id }}">[{{ $ja->kode }}] {{ $ja->nama }}</option>
                                @endforeach
                            </select>
                            <div wire:loading wire:target="jenis_akun_id" class="text-success">Memuat...</div>
                        </div>
                    </div>

                    <!-- Obyek Akun -->
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label>Obyek Akun</label>
                            <select wire:model.live="obyek_akun_id" class="form-select" wire:change="with">
                                <option value="">Pilih Obyek Akun</option>
                                @foreach ($obyekAkun as $oa)
                                <option value="{{ $oa->id }}">[{{ $oa->kode }}] {{ $oa->nama }}</option>
                                @endforeach
                            </select>
                            <div wire:loading wire:target="obyek_akun_id" class="text-success">Memuat...</div>
                        </div>
                    </div>

                    <!-- Rincian Obyek Akun -->
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label>Rincian Obyek Akun</label>
                            <select wire:model.live="rincian_obyek_akun_id" class="form-select" wire:change="with">
                                <option value="">Pilih Rincian Obyek Akun</option>
                                @foreach ($rincianObyekAkun as $roa)
                                <option value="{{ $roa->id }}">[{{ $roa->kode }}] {{ $roa->nama }}</option>
                                @endforeach
                            </select>
                            <div wire:loading wire:target="rincian_obyek_akun_id" class="text-success">Memuat...</div>
                        </div>
                    </div>

                    <!-- Sub Rincian Obyek Akun -->
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label>Sub Rincian Obyek Akun</label>
                            <select wire:model.live="sub_rincian_obyek_akun_id" class="form-select" wire:change="with">
                                <option value="">Pilih Sub Rincian Obyek Akun</option>
                                @foreach ($subRincianObyekAkun as $sroa)
                                <option value="{{ $sroa->id }}">[{{ $sroa->kode }}] {{ $sroa->nama }}</option>
                                @endforeach
                            </select>
                            <div wire:loading wire:target="sub_rincian_obyek_akun_id" class="text-success">Memuat...</div>
                        </div>
                    </div>

                    <!-- Tahun -->
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label>Tahun</label>
                            <select wire:model.live="tahun" class="form-select" wire:change="with">
                                <option value="">Pilih Tahun</option>
                                @for ($i = date('Y'); $i >= 2020; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            <div wire:loading wire:target="tahun" class="text-success">Memuat...</div>
                        </div>
                    </div>

                    <div class="col-md-6 align-content-end">
                        <div class="form-group mb-3 d-flex justify-content-end gap-2">
                            @if (!$realisasiAda)
                            {{-- <label>Cetak</label> --}}
                            <button wire:click="downloadExcel" class="btn btn-success btn-sm btn-block p-3">
                                <span class="btn-label">
                                    <i class="fa fa-print"></i>
                                </span>
                                Cetak Excel
                                <div wire:loading wire:target="downloadExcel" class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </button>
                            <!-- tombol Tampilkan Grafik, Tombol Tampilkan Tabel -->
                            <button wire:click="tampilkanRealisasi" class="btn btn-primary btn-sm btn-block p-3">
                                <span class="btn-label">
                                    <i class="fa fa-table"></i>
                                </span>
                                Tampilkan Tabel
                                <div wire:loading wire:target="tampilkanRealisasi" class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </button>
                            <button wire:click="tampilkanGrafik" class="btn btn-secondary btn-sm btn-block p-3">
                                <span class="btn-label">
                                    <i class="fa fa-chart-bar"></i>
                                </span>
                                Tampilkan Grafik
                                <div wire:loading wire:target="tampilkanGrafik" class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </button>
                            @else
                            <button wire:click="reload" class="btn btn-danger btn-sm btn-block p-3">
                                <span class="btn-label">
                                    <i class="fa fa-redo"></i>
                                </span>
                                Reload
                                <div wire:loading wire:target="reload" class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
    <div class="card-header" id="headingOne" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <div class="span-title">Grafik Anggaran & Realisasi Per Tahun</div>
            <div class="span-mode"></div>
        </div>
        <div class="card-body" wire:ignore>
            <canvas id="chartRealisasiBaru" style="max-height: 500px !important;"></canvas>
        </div>
        
    </div>
    <div class="card">
        <div class="card-header" id="headingOne" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <div class="span-title">Grafik Laporan</div>
            <div class="span-mode"></div>
        </div>

        <div class="card-body" wire:poll.5s>
            <div class="chart-container text-center">
                @if ($grafikAda == true)
                <h3 class="text-center badge bg-primary">REALISASI</h3>
                @endif
                <canvas wire:ignore id="chartRealisasi" style="max-height: 500px !important;"></canvas>
                <!-- warna daan labels -->
                <div class="row mt-3 text-center">
                    @if ($grafikAda == true)
                    <div class="col-lg-12  col-md-12 mb-2">
                        <table class="table table-striped table-hover">
                            <tbody class="align-left">
                                @foreach ($labels as $label)
                                <tr class="align-left">
                                    <td style="background-color: {{ $colors[$loop->index] }}; width: 20px;"></td>
                                    <td class="text-wrap">{{ $label }}</td>
                                    <td>Rp{{ number_format($data[$loop->index], 0, ",", ".") }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body" wire:poll.5s>
            <div class="chart-container text-center">
                @if ($grafikAda == true)
                <h3 class="text-center badge bg-primary">REALISASI</h3>
                @endif
                <canvas wire:ignore id="chartAkun" style="max-height: 500px !important;"></canvas>
                <!-- warna daan labels -->
                <div class="row mt-3 text-center">
                </div>
            </div>
        </div>
    </div>
    @if ($realisasiAda)

    <div class="card">
        <div class="card-header">
            <div class="span-title">Tabel Laporan</div>
            <div class="span-mode"></div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>SKPD</th>
                            <th>Kegiatan</th>
                            <th>Akun</th>
                            <th>Nilai Anggaran</th>
                            <th>Nilai Realisasi</th>
                            <th>Tahun</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-info">
                            <td colspan="4">Total</td>
                            <td>Rp {{ number_format($realisasi->sum(fn ($item) => $item->anggaran->rawNilaiAnggaran), 0, ",", ".") }}</td>
                            <td>Rp {{ number_format($realisasi->sum(fn ($item) => $item->rawNilaiRealisasi), 0, ",", ".") }}</td>
                            <td></td>
                        </tr>
                        @foreach ($realisasi as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                [{{ $item->anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->kode }}] {{ $item->anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->nama }}
                            </td>
                            <td>[{{ $item->anggaran->subKegiatan->kegiatan->kode }}] {{ $item->anggaran->subKegiatan->kegiatan->nama }}</td>
                            <td>[{{ $item->anggaran->subRincianObyekAkun->kode }}] {{ $item->anggaran->subRincianObyekAkun->nama }}</td>
                            <td>{{ $item->anggaran->nilai_anggaran }}</td>
                            <td>{{ $item->nilai_realisasi }}</td>
                            <td>{{ $item->tahun }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    @endif


    @push('script')
    <script>
        // grafik
        let chartRealisasi = null;

        document.addEventListener('livewire:init', function() {



            // Fungsi untuk membuat atau memperbarui chart
            function renderChart(labels, data, colors) {
                const ctx = document.getElementById('chartRealisasi').getContext('2d');

                if (!labels || !data || !colors || labels.length === 0 || data.length === 0 || colors.length === 0) {
                    console.error("Data chart tidak valid:", {
                        labels,
                        data,
                        colors
                    });
                    return;
                }

                if (chartRealisasi) {
                    chartRealisasi.data.labels = labels;
                    chartRealisasi.data.datasets[0].data = data;
                    chartRealisasi.data.datasets[0].backgroundColor = colors.slice(0, data.length);
                    chartRealisasi.data.datasets[0].borderColor = colors.slice(0, data.length).map(color => color.replace('1)', '0.8'));
                    chartRealisasi.update();


                } else {

                    // Jika chart belum ada, buat chart baru
                    chartRealisasi = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Nominal',
                                data: data,
                                backgroundColor: colors.slice(0, data.length),
                                borderColor: colors.slice(0, data.length).map(color => color.replace('1)', '0.8')),
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value, index, values) {
                                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            responsive: true,
                        }
                    });
                }
            }

            // Mendengarkan event Livewire untuk update data chart
            Livewire.on('tampilkanGrafik', function(response) {
                if (!Array.isArray(response) || response.length === 0) {
                    console.error("Response tidak valid:", response);
                    return;
                }

                const dataObject = response[0]; // Ambil objek pertama dari array
                const labels = dataObject.labels;
                const chartData = dataObject.data.map(Number); // Pastikan data numerik
                const colors = dataObject.colors;

                console.log("Data chart diterima dari Livewire:", dataObject);

                if (!labels || !chartData || !colors || labels.length === 0 || chartData.length === 0 || colors.length === 0) {
                    console.error("Data chart tidak valid:", {
                        labels,
                        chartData,
                        colors
                    });
                    return;
                }

                renderChart(labels, chartData, colors);
            });

            // Inisialisasi pertama kali dengan data awal
            const initialLabels = @json($labels);
            const initialData = @json($data);
            const initialColors = @json($colors);

            console.log(initialLabels, initialData, initialColors);

            renderChart(initialLabels, initialData, initialColors);


        });


        let chartRealisasiBaru = null;

        document.addEventListener('livewire:init', function() {
            function renderChartBaru(labels, dataRealisasi, dataAnggaran, colors) {
                const ctx = document.getElementById('chartRealisasiBaru').getContext('2d');

                if (!labels || !dataRealisasi || !dataAnggaran || !colors || labels.length === 0 || dataRealisasi.length === 0 || dataAnggaran.length === 0 || colors.length === 0) {
                    console.error("Data chart tidak valid:", {
                        labels,
                        dataRealisasi,
                        dataAnggaran,
                        colors
                    });
                    return;
                }

                if (chartRealisasiBaru) {
                    chartRealisasiBaru.data.labels = labels;
                    chartRealisasiBaru.data.datasets[0].data = dataRealisasi;
                    chartRealisasiBaru.data.datasets[1].data = dataAnggaran;
                    chartRealisasiBaru.data.datasets[0].backgroundColor = colors.slice(0, dataRealisasi.length);
                    chartRealisasiBaru.data.datasets[1].backgroundColor = colors.slice(0, dataAnggaran.length).map(color => color.replace('1)', '0.8'));
                    chartRealisasiBaru.update();
                } else {
                    // fungsi color random
                    function randomColor() {
                        return `rgba(${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, 1)`;
                    }
                    // Jika chart belum ada, buat chart baru
                    chartRealisasiBaru = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Nilai Anggaran',
                                    data: dataAnggaran,
                                    backgroundColor: randomColor(), // Warna berbeda untuk nilai anggaran
                                    borderColor: randomColor(),
                                    borderWidth: 1
                                },
                                {
                                    label: 'Nilai Realisasi',
                                    data: dataRealisasi,
                                    backgroundColor: randomColor(), // Warna berbeda untuk nilai realisasi
                                    borderColor: randomColor(),
                                    borderWidth: 1
                                },
                                
                            ]
                        },
                        options: {
                            plugins: {
                                legend: {
                                    labels: {
                                        font: {
                                            size: 12, // Custom font size
                                            weight: 'bold' // Custom font weight
                                        }
                                    }
                                },
                                tooltip: {
                                    titleFont: {
                                        size: 14
                                    },
                                    bodyFont: {
                                        size: 12
                                    }
                                }
                            },
                            responsive: true,
                        }
                    });
                }
            }

            // Mendengarkan event Livewire untuk update data chart baru
            Livewire.on('tampilkanGrafikBaru', (event) => {
                const { labels, dataRealisasi, dataAnggaran, colors } = event.detail;
                renderChartBaru(labels, dataRealisasi, dataAnggaran, colors);
            });

            // Inisialisasi pertama kali dengan data awal
            const initialLabels = @json($label_chart_tahunan);
            const initialDataRealisasi = @json($data_chart_realisasi_tahunan);
            const initialDataAnggaran = @json($data_chart_anggaran_tahunan);
            const initialColors = @json($colors_chart_tahunan);

            renderChartBaru(initialLabels, initialDataRealisasi, initialDataAnggaran, initialColors);
        });

        // akun chart
        let chartAkun = null;
        document.addEventListener('livewire:init', function() {
            function renderChartAkun(label_chart_akun, data_chart_realisasi_akun, data_chart_anggaran_akun, colors_chart_realisasi_akun, colors_chart_anggaran_akun) {
                const ctx3 = document.getElementById('chartAkun').getContext('2d');

                if (!label_chart_akun || !data_chart_realisasi_akun || !data_chart_anggaran_akun || !colors_chart_realisasi_akun || !colors_chart_anggaran_akun || label_chart_akun.length === 0 || data_chart_realisasi_akun.length === 0 || data_chart_anggaran_akun.length === 0 || colors_chart_realisasi_akun.length === 0 || colors_chart_anggaran_akun.length === 0) {
                    console.error("Data chart tidak valid:", {
                        label_chart_akun,
                        data_chart_realisasi_akun,
                        data_chart_anggaran_akun,
                        colors_chart_realisasi_akun,
                        colors_chart_anggaran_akun
                    });
                    return;
                }
                
                if (chartAkun) {
                    chartAkun.data.labels = label_chart_akun;
                    chartAkun.data.datasets[0].data = data_chart_realisasi_akun;
                    chartAkun.data.datasets[1].data = data_chart_anggaran_akun;
                    chartAkun.data.datasets[0].backgroundColor = colors_chart_realisasi_akun.slice(0, data_chart_realisasi_akun.length);
                    chartAkun.data.datasets[1].backgroundColor = colors_chart_anggaran_akun.slice(0, data_chart_anggaran_akun.length).map(color => color.replace('1)', '0.8'));
                    chartAkun.update();
                } else {
                    // Jika chart belum ada, buat chart baru
                    chartAkun = new Chart(ctx3, {
                        type: 'bar',
                        data: {
                            labels: label_chart_akun,
                            datasets: [
                                {
                                    label: 'Nilai Realisasi',
                                    data: data_chart_realisasi_akun,
                                    backgroundColor: colors_chart_realisasi_akun.slice(0, data_chart_realisasi_akun.length),
                                    borderColor: colors_chart_realisasi_akun.slice(0, data_chart_realisasi_akun.length).map(color => color.replace('1)', '0.8')),
                                    borderWidth: 1
                                },
                                {
                                    label: 'Nilai Anggaran',
                                    data: data_chart_anggaran_akun,
                                    backgroundColor: colors_chart_anggaran_akun.slice(0, data_chart_anggaran_akun.length),
                                    borderColor: colors_chart_anggaran_akun.slice(0, data_chart_anggaran_akun.length).map(color => color.replace('1)', '0.8')),
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value, index, values) {
                                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true
                                }
                            },
                            responsive: true,
                        }
                    });
                }
            }

            // Mendengarkan event Livewire untuk update data chart
            Livewire.on('tampilkanGrafikAkun', function(response) {
                if (!Array.isArray(response) || response.length === 0) {
                    console.error("Response tidak valid:", response);
                    return;
                }

                const dataObject = response[0]; // Ambil objek pertama dari array
                const label_chart_akun = dataObject.label_chart_akun;
                const data_chart_realisasi_akun = dataObject.data_chart_realisasi_akun.map(Number); // Pastikan data numerik
                const data_chart_anggaran_akun = dataObject.data_chart_anggaran_akun.map(Number); // Pastikan data numerik
                const colors_chart_realisasi_akun = dataObject.colors_chart_realisasi_akun;
                const colors_chart_anggaran_akun = dataObject.colors_chart_anggaran_akun;

                console.log("Data chart diterima dari Livewire:", dataObject);

                if (!label_chart_akun || !data_chart_realisasi_akun || !data_chart_anggaran_akun || !colors_chart_realisasi_akun || !colors_chart_anggaran_akun || label_chart_akun.length === 0 || data_chart_realisasi_akun.length === 0 || data_chart_anggaran_akun.length === 0 || colors_chart_realisasi_akun.length === 0 || colors_chart_anggaran_akun.length === 0) {
                    console.error("Data chart tidak valid:", {
                        label_chart_akun,
                        data_chart_realisasi_akun,
                        data_chart_anggaran_akun,
                        colors_chart_realisasi_akun,
                        colors_chart_anggaran_akun
                    });
                    return;
                }

                renderChartAkun(label_chart_akun, data_chart_realisasi_akun, data_chart_anggaran_akun, colors_chart_realisasi_akun, colors_chart_anggaran_akun);
            });
        });


        document.addEventListener('livewire:init', function() {
            Livewire.on('fileDownloaded', () => {
                window.location.reload();
            });

            Livewire.on('tambahAlertToast', (event) => {
                const data = event.detail;
                if (!data) {
                    console.error('Event detail is missing');
                    return;
                }
                swal({
                    title: data.title,
                    text: data.message,
                    icon: data.type,
                    buttons: {
                        confirm: {
                            text: "Ok",
                            value: true,
                            visible: true,
                            className: "btn btn-success",
                            closeModal: true
                        }
                    },
                    timer: 2500,
                    timerProgressBar: true
                });

                // setTimeout(function() {
                //     window.location.reload();
                // }, 2500);
            });

            Livewire.on('errorAlertToast', (event) => {
                const data = event;
                swal({
                    title: "Error",
                    text: "Terjadi kesalahan",
                    icon: "error",
                    buttons: {
                        confirm: {
                            text: "Ok",
                            value: true,
                            visible: true,
                            className: "btn btn-danger",
                            closeModal: true
                        }
                    },
                    timer: 2000,
                    timerProgressBar: true
                });
            });

            Livewire.on("toast", (event) => {
                toastr[event.notify](event.message);
            });
        });
    </script>
    @endpush
</div>