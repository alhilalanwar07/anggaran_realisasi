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
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $filter = 'urusan';

    public function with(): array
    {
        $grafikData = $this->grafikData();
        $pendapatanData = $this->pendapatanData();
        $belanjaData = $this->belanjaData();
        $semuaData = $this->semuaData($this->filter);
        return [
            'skpd' => Skpd::count(),
            'unit_skpd' => SubSkpd::count(),
            'program' => Program::count(),
            'kegiatan' => Kegiatan::count(),
            'sub_kegiatan' => SubKegiatan::count(),
            'pendapatan' => Anggaran::sum('nilai_anggaran'),
            'realisasi' => Realisasi::sum('nilai_realisasi'),
            'grafikData' => $grafikData,
            'pendapatanData' => $pendapatanData,
            'belanjaData' => $belanjaData,
            'filter' => $this->filter,
            'semuaData' => $semuaData
        ];
    }

    public function grafikData()
    {

        // value dari total nilai anggaran dari tabel anggaran berdasarkan kelompok_akun_id, yang beralasi 'subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun',
        $anggaranData = Anggaran::with('subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun')
            ->select('kelompok_akuns.nama', DB::raw('SUM(nilai_anggaran) as total'))
            ->join('sub_rincian_obyek_akuns', 'anggarans.sub_rincian_obyek_akun_id', '=', 'sub_rincian_obyek_akuns.id')
            ->join('rincian_obyek_akuns', 'sub_rincian_obyek_akuns.rincian_obyek_akun_id', '=', 'rincian_obyek_akuns.id')
            ->join('obyek_akuns', 'rincian_obyek_akuns.obyek_akun_id', '=', 'obyek_akuns.id')
            ->join('jenis_akuns', 'obyek_akuns.jenis_akun_id', '=', 'jenis_akuns.id')
            ->join('kelompok_akuns', 'jenis_akuns.kelompok_akun_id', '=', 'kelompok_akuns.id')
            ->groupBy('kelompok_akuns.nama', 'kelompok_akuns.id')
            ->get();

        $kelompokAkun = $anggaranData->pluck('nama')->toArray();
        $values = $anggaranData->pluck('total')->toArray();

        return [
            'labels' => $kelompokAkun,
            'values' => $values
        ];
    }

    // berdasarkan sumber dana
    //pendapatan = where kelompok = "Pendapatan"
    public function pendapatanData()
    {
        $anggaranData = Anggaran::with('subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun')
            ->select('kelompok_akuns.nama', DB::raw('SUM(nilai_anggaran) as total'))
            ->join('sub_rincian_obyek_akuns', 'anggarans.sub_rincian_obyek_akun_id', '=', 'sub_rincian_obyek_akuns.id')
            ->join('rincian_obyek_akuns', 'sub_rincian_obyek_akuns.rincian_obyek_akun_id', '=', 'rincian_obyek_akuns.id')
            ->join('obyek_akuns', 'rincian_obyek_akuns.obyek_akun_id', '=', 'obyek_akuns.id')
            ->join('jenis_akuns', 'obyek_akuns.jenis_akun_id', '=', 'jenis_akuns.id')
            ->join('kelompok_akuns', 'jenis_akuns.kelompok_akun_id', '=', 'kelompok_akuns.id')
            ->where('kelompok_akuns.nama', 'like', '%pendapatan%')
            ->groupBy('kelompok_akuns.nama', 'kelompok_akuns.id')
            ->get();

        return [
            'labels' => $anggaranData->pluck('nama')->toArray(),
            'values' => $anggaranData->pluck('total')->toArray()
        ];
    }

    //belanja = where kelompok = "Belanja"
    public function belanjaData()
    {
        $anggaranData = Anggaran::with('subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun')
            ->select('kelompok_akuns.nama', DB::raw('SUM(nilai_anggaran) as total'))
            ->join('sub_rincian_obyek_akuns', 'anggarans.sub_rincian_obyek_akun_id', '=', 'sub_rincian_obyek_akuns.id')
            ->join('rincian_obyek_akuns', 'sub_rincian_obyek_akuns.rincian_obyek_akun_id', '=', 'rincian_obyek_akuns.id')
            ->join('obyek_akuns', 'rincian_obyek_akuns.obyek_akun_id', '=', 'obyek_akuns.id')
            ->join('jenis_akuns', 'obyek_akuns.jenis_akun_id', '=', 'jenis_akuns.id')
            ->join('kelompok_akuns', 'jenis_akuns.kelompok_akun_id', '=', 'kelompok_akuns.id')
            ->where('kelompok_akuns.nama', 'like', '%belanja%')
            ->groupBy('kelompok_akuns.nama', 'kelompok_akuns.id')
            ->get();

        return [
            'labels' => $anggaranData->pluck('nama')->toArray(),
            'values' => $anggaranData->pluck('total')->toArray()
        ];
    }

    public $labels = [], $values = [], $colors = [];

    public function randomColors($count)
    {
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $colors[] = "#" . substr(str_shuffle("ABCDEF0123456789"), 0, 6);
        }
        return $colors;
    }

    public function semuaData($filter = 'urusan')
    {
        $query = Anggaran::with([
            'subKegiatan.kegiatan.program.subSkpd.skpd.urusanPelaksana.urusan',
            'subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun'
        ]);

        $query = $this->applyFilter($query, $filter);

        $data = $query->get();

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
        $this->semuaData($value);
    }
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
                    <span class="fw-bold">Hari ini:</span>
                </div>
                <div class="ms-3">
                    <span class="badge bg-primary">Rp {{ number_format($pendapatan, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-6 col-md-6">
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
        <div class="col-sm-6 col-md-6">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                <i class="fas fa-user-check"></i>
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
        {{-- <div class="col-sm-6 col-md-4">
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
                                <p class="card-category">Total Pembiayaan</p>
                                <h4 class="card-title">$ 1,345</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
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
                                <p class="card-category">Total Belanja</p>
                                <h4 class="card-title">576</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
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
                        <li><span class="label"><strong>Rekening Pendapatan</strong></span> <span class="value">348</span></li>
                        <li><span class="label"><strong>Rekening Belanja</strong></span> <span class="value">43796</span></li>
                        <li><span class="label"><strong>Rekening Pembiayaan</strong></span> <span class="value">42</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bagian Kanan -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-center mb-24 text-left">
                        <span class="badge bg-primary text-white fs-6" style="margin-left: 0px;">Total Belanja Per Sumber Dana</span>
                        <span class="badge bg-primary text-white fs-6">
                            <strong>
                                Rp {{ number_format(array_sum($grafikData['values']), 2, ',', '.') }}
                            </strong>
                        </span>
                    </div>
                    <div class="d-flex justify-content-around mb-24 mt-3">
                        <ul class="dashboard-stats mt-2">
                            <canvas id="kelompokChart"></canvas>

                        </ul>
                        <div class="">
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-sm table-hover">
                                    <tbody>
                                        @foreach ($grafikData['labels'] as $index => $label)
                                        @if ($index < 5)
                                            <tr>
                                            <td><strong>{{ $label }}</strong></td>
                                            <td class="text-end">Rp {{ number_format($grafikData['values'][$index], 2, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            @endforeach

                                            @if (count($grafikData['labels']) > 5)
                                            @foreach ($grafikData['labels'] as $index => $label)
                                            @if ($index >= 5)
                                            <tr>
                                                <td><strong>{{ $label }}</strong></td>
                                                <td class="text-end">Rp {{ number_format($grafikData['values'][$index], 2, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            @endforeach
                                            @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
                    <div class="d-flex justify-content-center mb-24 text-left">
                        <span class="badge bg-success text-white fs-6" style="margin-left: 0px;">Total Anggaran Belanja Berdasarkan Kelompok</span>
                        <span class="badge bg-success text-white fs-6">
                            <strong>
                                Rp {{ number_format(array_sum($belanjaData['values']), 2, ',', '.') }}
                            </strong>
                        </span>
                    </div>
                    <div class="d-flex justify-content-around mb-24 mt-3">
                        <ul class="dashboard-stats mt-2">
                            <canvas id="chartBelanjaGroup"></canvas>

                        </ul>
                        <div class="">
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-sm table-hover">
                                    <tbody>
                                        @foreach ($belanjaData['labels'] as $index => $label)
                                        @if ($index < 5)
                                            <tr>
                                            <td><strong>{{ $label }}</strong></td>
                                            <td class="text-end">Rp {{ number_format($belanjaData['values'][$index], 2, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            @endforeach

                                            @if (count($belanjaData['labels']) > 5)
                                            @foreach ($belanjaData['labels'] as $index => $label)
                                            @if ($index >= 5)
                                            <tr>
                                                <td><strong>{{ $label }}</strong></td>
                                                <td class="text-end">Rp {{ number_format($belanjaData['values'][$index], 2, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            @endforeach
                                            @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian Kanan -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-center mb-24 text-left">
                        <span class="badge bg-secondary text-white fs-6" style="margin-left: 0px;">Total Anggaran Pendapatan Berdasarkan Kelompok</span>
                        <span class="badge bg-secondary text-white fs-6">
                            <strong>
                                Rp {{ number_format(array_sum($pendapatanData['values']), 2, ',', '.') }}
                            </strong>
                        </span>
                    </div>
                    <div class="d-flex justify-content-around mb-24 mt-3">
                        <ul class="dashboard-stats mt-2">
                            <canvas id="chartPendapatanGroup"></canvas>

                        </ul>
                        <div class="">
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-sm table-hover">
                                    <tbody>
                                        @foreach ($pendapatanData['labels'] as $index => $label)
                                        @if ($index < 5)
                                            <tr>
                                            <td><strong>{{ $label }}</strong></td>
                                            <td class="text-end">Rp {{ number_format($pendapatanData['values'][$index], 2, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            @endforeach

                                            @if (count($pendapatanData['labels']) > 5)
                                            @foreach ($pendapatanData['labels'] as $index => $label)
                                            @if ($index >= 5)
                                            <tr>
                                                <td><strong>{{ $label }}</strong></td>
                                                <td class="text-end">Rp {{ number_format($pendapatanData['values'][$index], 2, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            @endforeach
                                            @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-2 mb-4">
        <!-- Bagian Kiri -->
        <div class="col-lg-9" wire:poll.5s>
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
                    <ul wire:ignore class="dashboard-stats mt-2" style="max-height: 300px; overflow-y: auto;">
                        <canvas id="barChart"></canvas>
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
        document.addEventListener('livewire:init', function() {
            // randomColor
            function randomColor() {
                return '#' + Math.floor(Math.random() * 16777215).toString(16);
            }

            const ctx = document.getElementById('kelompokChart').getContext('2d');
            const values = @json($grafikData['values']);
            const colors = values.map(() => randomColor());
            const chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: @json($grafikData['labels']),
                    datasets: [{
                        data: values,
                        backgroundColor: colors
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                },
            });

            const ctxPendapatanGroup = document.getElementById('chartPendapatanGroup').getContext('2d');
            new Chart(ctxPendapatanGroup, {
                type: 'pie',
                data: {
                    labels: @json($pendapatanData['labels']),
                    datasets: [{
                        data: @json($pendapatanData['values']),
                        backgroundColor: @json($pendapatanData['labels']).map(() => randomColor()),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                },
            });

            // Chart for Belanja
            const ctxBelanjaGroup = document.getElementById('chartBelanjaGroup').getContext('2d');
            new Chart(ctxBelanjaGroup, {
                type: 'pie',
                data: {
                    labels: @json($belanjaData['labels']),
                    datasets: [{
                        data: @json($belanjaData['values']),
                        backgroundColor: @json($belanjaData['labels']).map(() => randomColor()),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                },
            });
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

            if (!labels || !data || !colors || labels.length === 0 || data.length === 0 || colors.length === 0) {
                console.error("Data chart tidak valid:", {
                labels,
                data,
                colors
                });
                return;
            }

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
            if (!Array.isArray(response) || response.length === 0) {
                console.error("Response tidak valid:", response);
                return;
            }

            const dataObject = response[0];
            const labels = dataObject.labels;
            const chartData = dataObject.data.map(Number);
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

            const initialLabels = @json($labels);
            const initialData = @json($values);
            const initialColors = @json($colors);

            console.log(initialLabels, initialData, initialColors);

            renderChart(initialLabels, initialData, initialColors);
        });
    </script>
    @endpush
</div>