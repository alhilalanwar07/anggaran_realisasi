<?php

use Livewire\Volt\Component;
use App\Models\Skpd;
use App\Models\SubSkpd;
use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\Anggaran;
use App\Models\Realisasi;
use App\Models\KelompokAkun;
use App\Models\SubRincianObyekAkun;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $filter = 'urusan';
    public $tahun;
    public $label_chart_tahunan = [];
    public $data_chart_realisasi_tahunan = [];
    public $data_chart_anggaran_tahunan = [];
    public $colors_chart_tahunan = [];

    // untuk chart anggaran pendapatan
    public $label_chart_pendapatan = [];
    public $data_chart_pendapatan = [];
    public $color_chart_pendapatan = [];
    public $sum_total = 0;
    public $pendapatan_datas = [];

    // untuk chart anggaran belanja
    public $belanja_datas = [];
    public $label_chart_belanja = [], $data_chart_belanja = [], $color_chart_belanja = [];

    // untuk grafik data 
    public $label_grafik_data = [], $value_grafik_data = [], $color_grafik_data = [];
    public $labels = [], $values = [], $colors = [];


    public function mount()
    {
        $this->tahun = date('Y');
    }

    public function with(): array
    {
        $chartData = $this->chartPerTahun();
        $grafikData = $this->grafikData();
        // dd($pendapatanData);
        $belanjaData = $this->belanjaData();
        // dd($belanjaData);
        $semuaData = $this->semuaData($this->filter, $this->tahun);

        $dataAwalPendapatan = $this->dataAwalPendapatanData();
        $dataAwalBelanja = $this->dataAwalBelanjaData();

        // dd($dataAwalPendapatan);

        // dd($this->pendapatan_datas);
        return [
            'skpd' => Skpd::groupBy('kode')->count(),
            'unit_skpd' => SubSkpd::groupBy('kode')->count(),
            'program' => Program::groupBy('kode')->count(),
            'kegiatan' => Kegiatan::groupBy('kode')->count(),
            'sub_kegiatan' => SubKegiatan::groupBy('kode')->count(),
            'pendapatan' => Anggaran::where('tahun', 'like', $this->tahun)->whereHas('subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun', function ($query) {
                $query->where('nama', 'like', '%pendapatan%');
            })->sum('nilai_anggaran'),
            'pendapatan_berdasarkan_rekening' => $this->pendapatanData()->where('anggarans.tahun', 'like', $this->tahun)->get(),
            'belanja_berdasarkan_rekening' => $this->belanjaData()->where('anggarans.tahun', 'like', $this->tahun)->get(),
            'realisasi' => Realisasi::where('tahun', 'like', $this->tahun)->whereHas('anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun', function ($query) {
                $query->where('nama', 'like', '%belanja%');
            })->sum('nilai_realisasi'),
            'belanja' => Anggaran::where('tahun', 'like', $this->tahun)->whereHas('subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun', function ($query) {
                $query->where('nama', 'like', '%belanja%');
            })->sum('nilai_anggaran'),
            'total_anggaran' => Anggaran::where('tahun', 'like', $this->tahun)->sum('nilai_anggaran'),
            'total_realisasi' => Realisasi::where('tahun', 'like', $this->tahun)->sum('nilai_realisasi'),
            'grafikData' => $grafikData,
            'belanjaData' => $belanjaData,
            'filter' => $this->filter,
            'semuaData' => $semuaData,
            'tahun' => $this->tahun,
            'total_pembiayaan' => Anggaran::where('tahun', 'like', $this->tahun)->whereHas('subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun', function ($query) {
                $query->where('nama', 'like', '%pembiayaan%');
            })->sum('nilai_anggaran'),
            'total_penerimaan_pembiayaan' => Anggaran::where('tahun', 'like', $this->tahun)->whereHas('subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun', function ($query) {
                $query->where('nama', 'like', '%penerimaan pembiayaan%');
            })->sum('nilai_anggaran'),
            'total_pengeluaran_pembiayaan' => Anggaran::where('tahun', 'like', $this->tahun)->whereHas('subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun', function ($query) {
                $query->where('nama', 'like', '%pengeluaran pembiayaan%');
            })->sum('nilai_anggaran'),
            'total_pad' => Anggaran::where('tahun', 'like', $this->tahun)->whereHas('subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun', function ($query) {
                $query->where('nama', 'like', '%pad%');
            })->sum('nilai_anggaran'),
            'total_rekening_pendapatan' => SubRincianObyekAkun::whereHas('rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun', function ($query) {
                $query->where('nama', 'like', '%pendapatan%');
            })->count(),
            'total_rekening_belanja' => SubRincianObyekAkun::whereHas('rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun', function ($query) {
                $query->where('nama', 'like', '%belanja%');
            })->count(),
            'total_rekening_pembiayaan' => SubRincianObyekAkun::whereHas('rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun', function ($query) {
                $query->where('nama', 'like', '%pembiayaan%');
            })->count(),
            'total_sub_rincian_pembiayaan' => SubRincianObyekAkun::whereHas('rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun', function ($query) {
                $query->where('nama', 'like', '%pembiayaan%');
            })->count(),
            'label_chart_tahunan' => $chartData['label_chart_tahunan'],
            'data_chart_realisasi_tahunan' => $chartData['data_chart_realisasi_tahunan'],
            'data_chart_anggaran_tahunan' => $chartData['data_chart_anggaran_tahunan'],
            'colors_chart_tahunan' => $chartData['colors_chart_tahunan'],
            'label_chart_pendapatan' => $dataAwalPendapatan['label_chart_pendapatan'],
            'data_chart_pendapatan' => $dataAwalPendapatan['data_chart_pendapatan'],
            'color_chart_pendapatan' => $dataAwalPendapatan['color_chart_pendapatan'],
            'label_chart_belanja' => $dataAwalBelanja['label_chart_belanja'],
            'data_chart_belanja' => $dataAwalBelanja['data_chart_belanja'],
            'color_chart_belanja' => $dataAwalBelanja['color_chart_belanja'],
            'sum_total' => $dataAwalPendapatan['sum_total'],
            'total_anggaran_semua_tahun' => Anggaran::sum('nilai_anggaran'),
            'total_realisasi_semua_tahun' => Realisasi::sum('nilai_realisasi'),

            // 'pendapatan_datas' => $dataAwalPendapatan,
        ];
    }


    public function grafikData()
    {
        $anggaranData = Anggaran::with('subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun')
            ->select('kelompok_akuns.nama as nama', DB::raw('SUM(anggarans.nilai_anggaran) as total'))
            ->join('sub_rincian_obyek_akuns', 'anggarans.sub_rincian_obyek_akun_id', '=', 'sub_rincian_obyek_akuns.id')
            ->join('rincian_obyek_akuns', 'sub_rincian_obyek_akuns.rincian_obyek_akun_id', '=', 'rincian_obyek_akuns.id')
            ->join('obyek_akuns', 'rincian_obyek_akuns.obyek_akun_id', '=', 'obyek_akuns.id')
            ->join('jenis_akuns', 'obyek_akuns.jenis_akun_id', '=', 'jenis_akuns.id')
            ->join('kelompok_akuns', 'jenis_akuns.kelompok_akun_id', '=', 'kelompok_akuns.id')
            ->where('anggarans.tahun', 'like', $this->tahun)
            ->groupBy('kelompok_akuns.nama', 'kelompok_akuns.id')
            ->get();

        $this->label_grafik_data = $anggaranData->pluck('nama')->toArray();
        $this->value_grafik_data = $anggaranData->pluck('total')->toArray();
        $this->color_grafik_data = $this->randomColors(count($this->label_grafik_data));

        return [
            'label_grafik_data' => $this->label_grafik_data,
            'value_grafik_data' => $this->value_grafik_data,
            'color_grafik_data' => $this->color_grafik_data
        ];
    }

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

    public function dataAwalPendapatanData()
    {
        $this->pendapatan_datas = $this->pendapatanData()->where('anggarans.tahun', 'like', $this->tahun)->get();

        $this->label_chart_pendapatan = $this->pendapatan_datas->pluck('nama');
        $this->data_chart_pendapatan = $this->pendapatan_datas->pluck('total');
        $this->color_chart_pendapatan = $this->randomColors($this->pendapatan_datas->count());
        $this->sum_total = $this->pendapatan_datas->sum('total');

        return [
            'label_chart_pendapatan' => $this->label_chart_pendapatan,
            'data_chart_pendapatan' => $this->data_chart_pendapatan,
            'color_chart_pendapatan' => $this->color_chart_pendapatan,
            'sum_total' => $this->sum_total,
        ];
    }

    public function pendapatanData()
    {
        $anggaranData = KelompokAkun::with('jenisAkun.obyekAkun.rincianObyekAkun.subRincianObyekAkun.anggaran')
            ->select('kelompok_akuns.nama', DB::raw('SUM(anggarans.nilai_anggaran) as total'))
            ->join('jenis_akuns', 'kelompok_akuns.id', '=', 'jenis_akuns.kelompok_akun_id')
            ->join('obyek_akuns', 'jenis_akuns.id', '=', 'obyek_akuns.jenis_akun_id')
            ->join('rincian_obyek_akuns', 'obyek_akuns.id', '=', 'rincian_obyek_akuns.obyek_akun_id')
            ->join('sub_rincian_obyek_akuns', 'rincian_obyek_akuns.id', '=', 'sub_rincian_obyek_akuns.rincian_obyek_akun_id')
            ->join('anggarans', 'sub_rincian_obyek_akuns.id', '=', 'anggarans.sub_rincian_obyek_akun_id')
            ->where('kelompok_akuns.nama', 'like', '%pendapatan%')
            ->groupBy('kelompok_akuns.nama', 'kelompok_akuns.id');

        return $anggaranData;
    }



    public function dataAwalBelanjaData()
    {
        $this->belanja_datas = $this->belanjaData()->where('anggarans.tahun', 'like', $this->tahun)->get();

        $this->label_chart_belanja = $this->belanja_datas->pluck('nama');
        $this->data_chart_belanja = $this->belanja_datas->pluck('total');
        $this->color_chart_belanja = $this->randomColors($this->belanja_datas->count());

        return [
            'label_chart_belanja' => $this->label_chart_belanja,
            'data_chart_belanja' => $this->data_chart_belanja,
            'color_chart_belanja' => $this->color_chart_belanja,
        ];
    }

    public function belanjaData()
    {
        $belanjaData = Anggaran::with('subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun')
            ->select('kelompok_akuns.nama', DB::raw('SUM(nilai_anggaran) as total'))
            ->join('sub_rincian_obyek_akuns', 'anggarans.sub_rincian_obyek_akun_id', '=', 'sub_rincian_obyek_akuns.id')
            ->join('rincian_obyek_akuns', 'sub_rincian_obyek_akuns.rincian_obyek_akun_id', '=', 'rincian_obyek_akuns.id')
            ->join('obyek_akuns', 'rincian_obyek_akuns.obyek_akun_id', '=', 'obyek_akuns.id')
            ->join('jenis_akuns', 'obyek_akuns.jenis_akun_id', '=', 'jenis_akuns.id')
            ->join('kelompok_akuns', 'jenis_akuns.kelompok_akun_id', '=', 'kelompok_akuns.id')
            ->where('kelompok_akuns.nama', 'like', '%belanja%')
            ->groupBy('kelompok_akuns.nama', 'kelompok_akuns.id');

        return $belanjaData;
    }


    public function randomColors($count)
    {
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $colors[] = "#" . substr(str_shuffle("ABCDEF0123456789"), 0, 6);
        }
        return $colors;
    }

    

    public function semuaData($filter = 'urusan', $tahun = null)
    {
        $tahun = $tahun ?? $this->tahun;

        $query = Anggaran::with([
            'subKegiatan.kegiatan.program.subSkpd.skpd.urusanPelaksana.urusan',
            'subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun'
        ]);

        $query = $this->applyFilter($query, $filter);

        $data = $query->whereYear('anggarans.tahun', $tahun)->get();

        $this->labels = $data->pluck('nama')->toArray();
        $this->values = $data->pluck('total')->toArray();
        $this->colors = $this->randomColors(count($this->labels));

        $this->dispatch('filterChanged', [
            'labels' => $this->labels,
            'values' => $this->values,
            'colors' => $this->colors,
        ]);
    }

    private function applyFilter($query, $filter)
    {
        switch ($filter) {
            case 'urusan':
                return $query->select('urusans.nama', DB::raw('SUM(nilai_anggaran) as total'))
                    ->join('sub_kegiatans', 'anggarans.sub_kegiatan_id', '=', 'sub_kegiatans.id')
                    ->join('kegiatans', 'sub_kegiatans.kegiatan_id', '=', 'kegiatans.id')
                    ->join('programs', 'kegiatans.program_id', '=', 'programs.id')
                    ->join('sub_skpds', 'programs.sub_skpd_id', '=', 'sub_skpds.id')
                    ->join('skpds', 'sub_skpds.skpd_id', '=', 'skpds.id')
                    ->join('urusan_pelaksanas', 'skpds.urusan_pelaksana_id', '=', 'urusan_pelaksanas.id')
                    ->join('urusans', 'urusan_pelaksanas.urusan_id', '=', 'urusans.id')
                    ->groupBy('urusans.nama', 'urusans.id');
            case 'urusan_pelaksana':
                return $query->select('urusan_pelaksanas.nama', DB::raw('SUM(nilai_anggaran) as total'))
                    ->join('sub_kegiatans', 'anggarans.sub_kegiatan_id', '=', 'sub_kegiatans.id')
                    ->join('kegiatans', 'sub_kegiatans.kegiatan_id', '=', 'kegiatans.id')
                    ->join('programs', 'kegiatans.program_id', '=', 'programs.id')
                    ->join('sub_skpds', 'programs.sub_skpd_id', '=', 'sub_skpds.id')
                    ->join('skpds', 'sub_skpds.skpd_id', '=', 'skpds.id')
                    ->join('urusan_pelaksanas', 'skpds.urusan_pelaksana_id', '=', 'urusan_pelaksanas.id')
                    ->groupBy('urusan_pelaksanas.nama', 'urusan_pelaksanas.id');
            case 'skpd':
                return $query->select('skpds.nama', DB::raw('SUM(nilai_anggaran) as total'))
                    ->join('sub_kegiatans', 'anggarans.sub_kegiatan_id', '=', 'sub_kegiatans.id')
                    ->join('kegiatans', 'sub_kegiatans.kegiatan_id', '=', 'kegiatans.id')
                    ->join('programs', 'kegiatans.program_id', '=', 'programs.id')
                    ->join('sub_skpds', 'programs.sub_skpd_id', '=', 'sub_skpds.id')
                    ->join('skpds', 'sub_skpds.skpd_id', '=', 'skpds.id')
                    ->groupBy('skpds.nama', 'skpds.id');
            case 'sub_skpd':
                return $query->select('sub_skpds.nama', DB::raw('SUM(nilai_anggaran) as total'))
                    ->join('sub_kegiatans', 'anggarans.sub_kegiatan_id', '=', 'sub_kegiatans.id')
                    ->join('kegiatans', 'sub_kegiatans.kegiatan_id', '=', 'kegiatans.id')
                    ->join('programs', 'kegiatans.program_id', '=', 'programs.id')
                    ->join('sub_skpds', 'programs.sub_skpd_id', '=', 'sub_skpds.id')
                    ->groupBy('sub_skpds.nama', 'sub_skpds.id');
            case 'program':
                return $query->select('programs.nama', DB::raw('SUM(nilai_anggaran) as total'))
                    ->join('sub_kegiatans', 'anggarans.sub_kegiatan_id', '=', 'sub_kegiatans.id')
                    ->join('kegiatans', 'sub_kegiatans.kegiatan_id', '=', 'kegiatans.id')
                    ->join('programs', 'kegiatans.program_id', '=', 'programs.id')
                    ->groupBy('programs.nama', 'programs.id');
            case 'kegiatan':
                return $query->select('kegiatans.nama', DB::raw('SUM(nilai_anggaran) as total'))
                    ->join('sub_kegiatans', 'anggarans.sub_kegiatan_id', '=', 'sub_kegiatans.id')
                    ->join('kegiatans', 'sub_kegiatans.kegiatan_id', '=', 'kegiatans.id')
                    ->groupBy('kegiatans.nama', 'kegiatans.id');
            case 'sub_kegiatan':
                return $query->select('sub_kegiatans.nama', DB::raw('SUM(nilai_anggaran) as total'))
                    ->join('sub_kegiatans', 'anggarans.sub_kegiatan_id', '=', 'sub_kegiatans.id')
                    ->groupBy('sub_kegiatans.nama', 'sub_kegiatans.id');
            case 'kelompok_akun':
                return $query->select('kelompok_akuns.nama', DB::raw('SUM(nilai_anggaran) as total'))
                    ->join('sub_rincian_obyek_akuns', 'anggarans.sub_rincian_obyek_akun_id', '=', 'sub_rincian_obyek_akuns.id')
                    ->join('rincian_obyek_akuns', 'sub_rincian_obyek_akuns.rincian_obyek_akun_id', '=', 'rincian_obyek_akuns.id')
                    ->join('obyek_akuns', 'rincian_obyek_akuns.obyek_akun_id', '=', 'obyek_akuns.id')
                    ->join('jenis_akuns', 'obyek_akuns.jenis_akun_id', '=', 'jenis_akuns.id')
                    ->join('kelompok_akuns', 'jenis_akuns.kelompok_akun_id', '=', 'kelompok_akuns.id')
                    ->groupBy('kelompok_akuns.nama', 'kelompok_akuns.id');
            case 'jenis_akun':
                return $query->select('jenis_akuns.nama', DB::raw('SUM(nilai_anggaran) as total'))
                    ->join('sub_rincian_obyek_akuns', 'anggarans.sub_rincian_obyek_akun_id', '=', 'sub_rincian_obyek_akuns.id')
                    ->join('rincian_obyek_akuns', 'sub_rincian_obyek_akuns.rincian_obyek_akun_id', '=', 'rincian_obyek_akuns.id')
                    ->join('obyek_akuns', 'rincian_obyek_akuns.obyek_akun_id', '=', 'obyek_akuns.id')
                    ->join('jenis_akuns', 'obyek_akuns.jenis_akun_id', '=', 'jenis_akuns.id')
                    ->groupBy('jenis_akuns.nama', 'jenis_akuns.id');
            case 'obyek_akun':
                return $query->select('obyek_akuns.nama', DB::raw('SUM(nilai_anggaran) as total'))
                    ->join('sub_rincian_obyek_akuns', 'anggarans.sub_rincian_obyek_akun_id', '=', 'sub_rincian_obyek_akuns.id')
                    ->join('rincian_obyek_akuns', 'sub_rincian_obyek_akuns.rincian_obyek_akun_id', '=', 'rincian_obyek_akuns.id')
                    ->join('obyek_akuns', 'rincian_obyek_akuns.obyek_akun_id', '=', 'obyek_akuns.id')
                    ->groupBy('obyek_akuns.nama', 'obyek_akuns.id');
            case 'rincian_obyek_akun':
                return $query->select('rincian_obyek_akuns.nama', DB::raw('SUM(nilai_anggaran) as total'))
                    ->join('sub_rincian_obyek_akuns', 'anggarans.sub_rincian_obyek_akun_id', '=', 'sub_rincian_obyek_akuns.id')
                    ->join('rincian_obyek_akuns', 'sub_rincian_obyek_akuns.rincian_obyek_akun_id', '=', 'rincian_obyek_akuns.id')
                    ->groupBy('rincian_obyek_akuns.nama', 'rincian_obyek_akuns.id');
            case 'sub_rincian_obyek_akun':
                return $query->select('sub_rincian_obyek_akuns.nama', DB::raw('SUM(nilai_anggaran) as total'))
                    ->join('sub_rincian_obyek_akuns', 'anggarans.sub_rincian_obyek_akun_id', '=', 'sub_rincian_obyek_akuns.id')
                    ->groupBy('sub_rincian_obyek_akuns.nama', 'sub_rincian_obyek_akuns.id');
            default:
                return $query->select('urusans.nama', DB::raw('SUM(nilai_anggaran) as total'))
                    ->join('sub_kegiatans', 'anggarans.sub_kegiatan_id', '=', 'sub_kegiatans.id')
                    ->join('kegiatans', 'sub_kegiatans.kegiatan_id', '=', 'kegiatans.id')
                    ->join('programs', 'kegiatans.program_id', '=', 'programs.id')
                    ->join('sub_skpds', 'programs.sub_skpd_id', '=', 'sub_skpds.id')
                    ->join('skpds', 'sub_skpds.skpd_id', '=', 'skpds.id')
                    ->join('urusan_pelaksanas', 'skpds.urusan_pelaksana_id', '=', 'urusan_pelaksanas.id')
                    ->join('urusans', 'urusan_pelaksanas.urusan_id', '=', 'urusans.id')
                    ->groupBy('urusans.nama', 'urusans.id');
        }
    }

    public function updatedFilter($value)
    {
        $this->filter = $value;
        $this->semuaData($this->filter, $this->tahun);
    }


    public function updatedTahun($value)
    {
        $this->tahun = $value;
        $this->semuaData($this->filter, $this->tahun);

        $this->grafikData();
        $this->chartPerTahun();

        $this->perbaharuiChart();

        $this->pendapatan_datas = $this->pendapatanData()->where('anggarans.tahun', 'like', $this->tahun)->get();
        $this->belanja_datas = $this->belanjaData()->where('anggarans.tahun', 'like', $this->tahun)->get();
    }

    public function perbaharuiChart()
    {
        $this->pendapatan_datas = $this->pendapatanData()->where('anggarans.tahun', 'like', $this->tahun)->get();

        if ($this->pendapatan_datas->count() > 0) {
            $this->label_chart_pendapatan = $this->pendapatan_datas->pluck('nama');
            $this->data_chart_pendapatan = $this->pendapatan_datas->pluck('total');
            $this->color_chart_pendapatan = $this->randomColors($this->pendapatan_datas->count());
            $this->sum_total = $this->pendapatan_datas->sum('total');

            // Dispatch event ke klien
            $this->dispatch('tampilkanGrafik', [
                'label_chart_pendapatan' => $this->label_chart_pendapatan,
                'data_chart_pendapatan' => $this->data_chart_pendapatan,
                'color_chart_pendapatan' => $this->color_chart_pendapatan,
                'sum_total' => $this->sum_total,
            ]);
        } else {
            // Kirimkan event untuk membersihkan grafik jika data kosong
            $this->dispatch('tampilkanGrafik', [
                'label_chart_pendapatan' => ['Tidak ada data pendapatan di tahun ' . $this->tahun],
                'data_chart_pendapatan' => ['0'],
                'color_chart_pendapatan' => ['#000'],
            ]);
        }

        $this->belanja_datas = $this->belanjaData()->where('anggarans.tahun', 'like', $this->tahun)->get();

        if ($this->belanja_datas->count() > 0) {
            $this->label_chart_belanja = $this->belanja_datas->pluck('nama');
            $this->data_chart_belanja = $this->belanja_datas->pluck('total');
            $this->color_chart_belanja = $this->randomColors($this->belanja_datas->count());

            // Dispatch event ke klien
            $this->dispatch('tampilkanGrafikBelanja', [
                'label_chart_belanja' => $this->label_chart_belanja,
                'data_chart_belanja' => $this->data_chart_belanja,
                'color_chart_belanja' => $this->color_chart_belanja,
            ]);
        } else {
            // Kirimkan event untuk membersihkan grafik jika data kosong
            $this->dispatch('tampilkanGrafikBelanja', [
                'label_chart_belanja' => ['Tidak ada data belanja di tahun ' . $this->tahun],
                'data_chart_belanja' => ['0'],
                'color_chart_belanja' => ['#000'],
            ]);
        }
    }

    // 
}; ?>

<div>
    <style>
        .dashboard-stats {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .dashboard-stats li {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }

        .dashboard-stats .label {
            flex: 1;
            text-align: left;
        }

        .dashboard-stats .value {
            flex: 1;
            text-align: right;
        }
    </style>
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
            <h3 class="fw-bold mb-3">Dashboard</h3>
            <h6 class="op-7 mb-2">
                Selamat datang di dashboard aplikasi pengelolaan anggaran daerah. Berikut adalah data-data terkait
                pengelolaan anggaran daerah.
            </h6>
        </div>

        <div class="ms-md-auto">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center">
                    <div class="me-2">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <span class="fw-bold">Tahun:</span>
                </div>
                <div class="ms-3 d-flex align-items-center gap-2">
                    <select wire:model.live="tahun" class="form-select">
                        @for ($i = 2021; $i <= date('Y'); $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                    </select>
                    <button wire:click="perbaharuiChart" class="btn btn-primary btn-sm">
                        <i class="fas fa-sync"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                <i class="fas fa-luggage-cart"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Total Anggaran</p>
                                <h4 class="card-title">
                                    Rp {{ number_format($total_anggaran, 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                <i class="far fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Total Realisasi</p>
                                <h4 class="card-title">
                                    Rp {{ number_format($total_realisasi, 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Total Pendapatan</p>
                                <h4 class="card-title">
                                    Rp {{ number_format($pendapatan, 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Total Belanja</p>
                                <h4 class="card-title">
                                    Rp {{ number_format($realisasi, 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Total Pembiayaan</p>
                                <h4 class="card-title">
                                    Rp {{ number_format($total_pembiayaan, 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-warning bubble-shadow-small">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Total Penerimaan Pembiayaan</p>
                                <h4 class="card-title">
                                    Rp {{ number_format($total_penerimaan_pembiayaan, 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-danger bubble-shadow-small">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Total Pengeluaran Pembiayaan</p>
                                <h4 class="card-title">
                                    Rp {{ number_format($total_pengeluaran_pembiayaan, 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Total Pendapatan Asli Daerah (PAD)</p>
                                <h4 class="card-title">
                                    Rp {{ number_format($total_pad, 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row g-2 mb-4">
        <!-- Bagian Kiri -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge bg-warning text-white" style="margin-left: 0px;">Data</span>
                        <span class="badge bg-success text-white">Jumlah</span>
                    </div>
                    <ul class="dashboard-stats mt-2">
                        <li><span class="label"><strong>SKPD</strong></span> <span class="value">
                                {{ $skpd }}
                            </span></li>
                        <li><span class="label"><strong>Unit SKPD</strong></span> <span class="value">{{ $unit_skpd }}</span></li>
                        <li><span class="label"><strong>Program</strong></span> <span class="value">{{ $program }}</span></li>
                        <li><span class="label"><strong>Kegiatan</strong></span> <span class="value">{{ $kegiatan }}</span></li>
                        <li><span class="label"><strong>Sub Kegiatan</strong></span> <span class="value">{{ $sub_kegiatan }}</span></li>
                        <li><span class="label"><strong>Rekening Pendapatan</strong></span> <span class="value">
                                {{ $total_rekening_pendapatan }}
                            </span></li>
                        <li><span class="label"><strong>Rekening Belanja</strong></span> <span class="value">
                                {{ $total_rekening_belanja }}
                            </span></li>
                        <li><span class="label"><strong>Rekening Pembiayaan</strong></span> <span class="value">
                                {{ $total_rekening_pembiayaan }}
                            </span></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bagian Kanan -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-center mb-24 text-left scroll flex-column flex-md-row">
                        <span class="badge bg-primary text-white fs-6 mb-2 mb-md-0" style="margin-left: 0px;">Total Anggaran & Realisasi Per Tahun</span>
                        <span class="badge bg-primary text-white fs-6">
                            <strong>
                                Rp {{ number_format($total_anggaran_semua_tahun, 0, ',', '.') }} / Rp {{ number_format($total_realisasi_semua_tahun, 0, ',', '.') }}
                            </strong>
                        </span>
                    </div>
                    <div class=" mb-2 mt-3">
                        <ul class="dashboard-stats mt-2" wire.ignore style="max-height: 300px !important;">
                            <canvas id="chartRealisasiBaru" style="max-height: 300px !important;"></canvas>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-2 mb-4">
        <!-- Bagian Kiri -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-center mb-24 text-left scroll flex-column flex-md-row">
                        <span class="badge bg-success text-white fs-6" style="margin-left: 0px;">Total Anggaran Belanja Berdasarkan Kelompok</span>
                        <span class="badge bg-success text-white fs-6">
                            <strong>
                                Rp {{ number_format($belanja, 0, ',', '.') }}
                            </strong>
                        </span>
                    </div>
                    <div class="d-flex justify-content-around mb-2 mt-3 scroll flex-column flex-md-row">
                        <ul class="dashboard-stats mt-2" wire:ignore style="max-height: 300px !important; width: 100%;">
                            <canvas id="chartBelanjaGroup"></canvas>
                        </ul>
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-hover">
                                <tbody>
                                    @forelse($belanja_berdasarkan_rekening as $item)
                                    <tr>
                                        <td><strong>{{ $item->nama }}</strong></td>
                                        <td class="text-end">Rp {{ number_format($item->total, 2, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format(($item->total / $realisasi) * 100, 2, ',', '.') }}%</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="text-center">Tidak ada anggaran belanja tahun {{ $tahun }}</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian Kanan -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-center mb-24 text-left scroll flex-column flex-md-row">
                        <span class="badge bg-secondary text-white fs-6" style="margin-left: 0px;">Total Anggaran Pendapatan Berdasarkan Kelompok</span>
                        <span class="badge bg-secondary text-white fs-6">
                            <strong>
                                Rp {{ number_format($pendapatan, 0, ',', '.') }}
                            </strong>
                        </span>
                    </div>
                    <div class="d-flex justify-content-around mb-2 mt-3 scroll flex-column flex-md-row">
                        <ul class="dashboard-stats mt-2" wire.ignore style="max-height: 300px !important; width: 100%;">
                            <canvas id="chartPendapatanGroup"></canvas>
                        </ul>
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-hover">
                                <tbody>
                                    @forelse($pendapatan_berdasarkan_rekening as $item)
                                    <tr>
                                        <td><strong>{{ $item->nama }}</strong></td>
                                        <td class="text-end">Rp {{ number_format($item->total, 2, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format(($item->total / $pendapatan) * 100, 2, ',', '.') }}%</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="text-center">Tidak ada anggaran pendapatan tahun {{ $tahun }}</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-2 mb-4">
        <!-- Bagian Kiri -->
        <div class="col-lg-9">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-center mb-24 text-left">
                        <span class="badge bg-success text-white fs-6" style="margin-left: 0px;">
                            Grafik Anggaran Per
                            @if ($filter == 'urusan')
                            Urusan
                            @elseif ($filter == 'urusan_pelaksana')
                            Urusan Pelaksana
                            @elseif ($filter == 'skpd')
                            SKPD
                            @elseif ($filter == 'sub_skpd')
                            Sub SKPD
                            @elseif ($filter == 'program')
                            Program
                            @elseif ($filter == 'kegiatan')
                            Kegiatan
                            @elseif ($filter == 'sub_kegiatan')
                            Sub Kegiatan
                            @elseif ($filter == 'kelompok_akun')
                            Kelompok Akun
                            @elseif ($filter == 'jenis_akun')
                            Jenis Akun
                            @elseif ($filter == 'obyek_akun')
                            Obyek Akun
                            @elseif ($filter == 'rincian_obyek_akun')
                            Rincian Obyek Akun
                            @elseif ($filter == 'sub_rincian_obyek_akun')
                            Sub Rincian Obyek Akun
                            @endif
                        </span>
                    </div>
                    <ul class="dashboard-stats mt-2" style="max-height: 300px; overflow-y: auto;">
                        <canvas wire:ignore id="barChart"></canvas>
                    </ul>
                    <div class="d-flex justify-content-around mb-24 mt-3" style="max-height: 300px; overflow-y: auto;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian Kanan -->
        <div class="col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-center mb-24 text-left">
                        <span class="badge bg-secondary text-white fs-6" style="margin-left: 0px;">Filter Grafik</span>
                    </div>
                    <div class="" style="max-height: 300px; overflow-y: auto;">
                        <div class="form-check pb-0">
                            <input wire:model.live="filter" class="form-check-input" type="radio" name="filter" value="urusan" id="urusan" checked>
                            <label class="form-check-label" for="urusan">Urusan</label>
                        </div>
                        <div class="form-check pb-0">
                            <input wire:model.live="filter" class="form-check-input" type="radio" name="filter" value="urusan_pelaksana" id="urusan_pelaksana">
                            <label class="form-check-label" for="urusan_pelaksana">Urusan Pelaksana</label>
                        </div>
                        <div class="form-check pb-0">
                            <input wire:model.live="filter" class="form-check-input" type="radio" name="filter" value="skpd" id="skpd">
                            <label class="form-check-label" for="skpd">SKPD</label>
                        </div>
                        <div class="form-check pb-0">
                            <input wire:model.live="filter" class="form-check-input" type="radio" name="filter" value="sub_skpd" id="sub_skpd">
                            <label class="form-check-label" for="sub_skpd">Sub SKPD</label>
                        </div>
                        <div class="form-check pb-0">
                            <input wire:model.live="filter" class="form-check-input" type="radio" name="filter" value="program" id="program">
                            <label class="form-check-label" for="program">Program</label>
                        </div>
                        <div class="form-check pb-0">
                            <input wire:model.live="filter" class="form-check-input" type="radio" name="filter" value="kegiatan" id="kegiatan">
                            <label class="form-check-label" for="kegiatan">Kegiatan</label>
                        </div>
                        <div class="form-check pb-0">
                            <input wire:model.live="filter" class="form-check-input" type="radio" name="filter" value="sub_kegiatan" id="sub_kegiatan">
                            <label class="form-check-label" for="sub_kegiatan">Sub Kegiatan</label>
                        </div>
                        <div class="form-check pb-0">
                            <input wire:model.live="filter" class="form-check-input" type="radio" name="filter" value="kelompok_akun" id="kelompok_akun">
                            <label class="form-check-label" for="kelompok_akun">Kelompok Akun</label>
                        </div>
                        <div class="form-check pb-0">
                            <input wire:model.live="filter" class="form-check-input" type="radio" name="filter" value="jenis_akun" id="jenis_akun">
                            <label class="form-check-label" for="jenis_akun">Jenis Akun</label>
                        </div>
                        <div class="form-check pb-0">
                            <input wire:model.live="filter" class="form-check-input" type="radio" name="filter" value="obyek_akun" id="obyek_akun">
                            <label class="form-check-label" for="obyek_akun">Obyek Akun</label>
                        </div>
                        <div class="form-check pb-0">
                            <input wire:model.live="filter" class="form-check-input" type="radio" name="filter" value="rincian_obyek_akun" id="rincian_obyek_akun">
                            <label class="form-check-label" for="rincian_obyek_akun">Rincian Obyek Akun</label>
                        </div>
                        <div class="form-check pb-0">
                            <input wire:model.live="filter" class="form-check-input" type="radio" name="filter" value="sub_rincian_obyek_akun" id="sub_rincian_obyek_akun">
                            <label class="form-check-label" for="sub_rincian_obyek_akun">Sub Rincian Obyek Akun</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let chartRealisasiBaru = null;

        document.addEventListener('livewire:init', function() {
            function renderChartBaru(labels, dataRealisasi, dataAnggaran, colors) {
                const ctx = document.getElementById('chartRealisasiBaru').getContext('2d');

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
                            datasets: [{
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
                const {
                    labels,
                    dataRealisasi,
                    dataAnggaran,
                    colors
                } = event.detail;
                renderChartBaru(labels, dataRealisasi, dataAnggaran, colors);
            });

            // Inisialisasi pertama kali dengan data awal
            const initialLabels = @json($label_chart_tahunan);
            const initialDataRealisasi = @json($data_chart_realisasi_tahunan);
            const initialDataAnggaran = @json($data_chart_anggaran_tahunan);
            const initialColors = @json($colors_chart_tahunan);

            renderChartBaru(initialLabels, initialDataRealisasi, initialDataAnggaran, initialColors);

        });

        // chart pendapatanData
        let chartPendapatanGroup = null;

        document.addEventListener('livewire:init', function() {
            // data awal
            const initialLabels = @json($label_chart_pendapatan);
            const initialData = @json($data_chart_pendapatan);
            const initialColors = @json($color_chart_pendapatan);

            // Fungsi untuk membuat atau memperbarui chart
            function renderChart(labels, data, colors) {
                const ctx = document.getElementById('chartPendapatanGroup').getContext('2d');

                if (chartPendapatanGroup) {
                    chartPendapatanGroup.data.labels = labels;
                    chartPendapatanGroup.data.datasets[0].data = data;
                    chartPendapatanGroup.data.datasets[0].backgroundColor = colors.slice(0, data.length);
                    chartPendapatanGroup.update();
                } else {
                    // Jika chart belum ada, buat chart baru
                    chartPendapatanGroup = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Nominal',
                                data: data,
                                backgroundColor: colors.slice(0, data.length),
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            return 'Rp ' + tooltipItem.raw.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Mendengarkan event Livewire untuk update data chart
            Livewire.on('tampilkanGrafik', function(response) {

                const dataObject = response[0];
                const label_chart_pendapatan = dataObject.label_chart_pendapatan;
                const data_chart_pendapatan = dataObject.data_chart_pendapatan.map(Number);
                const color_chart_pendapatan = dataObject.color_chart_pendapatan;

                if (!label_chart_pendapatan || !data_chart_pendapatan || !color_chart_pendapatan || label_chart_pendapatan.length === 0 || data_chart_pendapatan.length === 0 || color_chart_pendapatan.length === 0) {
                    console.error("Data chart tidak valid:", {
                        label_chart_pendapatan,
                        data_chart_pendapatan,
                        color_chart_pendapatan
                    });
                    return;
                }
                renderChart(label_chart_pendapatan, data_chart_pendapatan, color_chart_pendapatan);
            });

            // Render chart pertama kali
            renderChart(initialLabels, initialData, initialColors);
        });

        // chart chartBelanjaGroup
        let chartBelanjaGroup = null;

        document.addEventListener('livewire:init', function() {
            // data awal
            const initialLabels = @json($label_chart_belanja);
            const initialData = @json($data_chart_belanja);
            const initialColors = @json($color_chart_belanja);

            function renderChart(labels, data, colors) {
                const ctx = document.getElementById('chartBelanjaGroup').getContext('2d');

                if (chartBelanjaGroup) {
                    chartBelanjaGroup.data.labels = labels;
                    chartBelanjaGroup.data.datasets[0].data = data;
                    chartBelanjaGroup.data.datasets[0].backgroundColor = colors.slice(0, data.length);
                    chartBelanjaGroup.update();
                } else {
                    // Jika chart belum ada, buat chart baru
                    chartBelanjaGroup = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Nominal',
                                data: data,
                                backgroundColor: colors.slice(0, data.length),
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            return 'Rp ' + tooltipItem.raw.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Mendengarkan event Livewire untuk update data chart
            Livewire.on('tampilkanGrafikBelanja', function(response) {
                const dataObject = response[0];
                const label_chart_belanja = dataObject.label_chart_belanja;
                const data_chart_belanja = dataObject.data_chart_belanja.map(Number);
                const color_chart_belanja = dataObject.color_chart_belanja;

                renderChart(label_chart_belanja, data_chart_belanja, color_chart_belanja);
            });

            // Render chart pertama kali
            renderChart(initialLabels, initialData, initialColors);
        });
    </script>
    <script>
        let barChart = null;

        document.addEventListener('livewire:init', function() {
            function randomColor() {
                return '#' + Math.floor(Math.random() * 16777215).toString(16);
            }

            function renderChart(labels, data, colors) {
                const ctx = document.getElementById('barChart').getContext('2d');

                if (barChart) {
                    barChart.data.labels = labels;
                    barChart.data.datasets[0].data = data;
                    barChart.data.datasets[0].backgroundColor = colors.slice(0, data.length);
                    barChart.data.datasets[0].borderColor = colors.slice(0, data.length).map(color => color.replace('1)', '0.8'));
                    barChart.update();
                } else {
                    barChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Jumlah Anggaran',
                                data: data,
                                backgroundColor: colors.slice(0, data.length),
                                borderColor: colors.slice(0, data.length).map(color => color.replace('1)', '0.8')),
                                borderWidth: 1
                            }]
                        },
                        options: {
                            plugins: {
                                legend: {
                                    labels: {
                                        font: {
                                            family: "'Montserrat', cursive, sans-serif",
                                            size: 12,
                                            weight: 'bold'
                                        }
                                    }
                                },
                                tooltip: {
                                    titleFont: {
                                        family: "'Montserrat', monospace",
                                        size: 14
                                    },
                                    bodyFont: {
                                        family: "'Montserrat', sans-serif",
                                        size: 12
                                    }
                                }
                            },
                            responsive: true,
                            scales: {
                                y: {
                                    ticks: {
                                        font: {
                                            family: "'Montserrat', sans-serif",
                                            size: 12,
                                            weight: 'bold'
                                        }
                                    },
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Jumlah Anggaran',
                                        font: {
                                            family: "'Montserrat', sans-serif",
                                            size: 12,
                                            weight: 'bold'
                                        }
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            family: "'Montserrat', sans-serif",
                                            size: 12,
                                            weight: 'bold'
                                        }
                                    },
                                    title: {
                                        display: true,
                                        text: 'Calon',
                                        font: {
                                            family: "'Montserrat', sans-serif",
                                            size: 14,
                                            weight: 'bold'
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }

            Livewire.on('filterChanged', function(response) {

                const dataObject = response[0];
                const labels = dataObject.labels;
                const chartData = dataObject.data.map(Number);
                const colors = dataObject.colors;

                renderChart(labels, chartData, colors);
            });

            const initialLabels = @json($labels);
            const initialData = @json($values);
            const initialColors = @json($colors);

            console.log(initialLabels, initialData, initialColors);

            renderChart(initialLabels, initialData, initialColors);
        });
    </script>
    @endpush
</div>