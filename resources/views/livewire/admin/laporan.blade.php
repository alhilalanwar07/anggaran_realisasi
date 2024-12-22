<?php

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
use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Asantibanez\LivewireCharts\Models\PieChartModel;
use Asantibanez\LivewireCharts\Models\LineChartModel;



new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

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
    public $tahun;
    public $realisasi;
    public $anggaran_id;

    public function with(): array
    {
        $query = Realisasi::query()
                ->with(['anggaran.subKegiatan.kegiatan.program.subSkpd.skpd.urusanPelaksana.urusan',
                    'anggaran.subKegiatan.kegiatan.program.subSkpd.skpd.urusanPelaksana',
                    'anggaran.subKegiatan.kegiatan.program.subSkpd.skpd',
                    'anggaran.subKegiatan.kegiatan.program.subSkpd',
                    'anggaran.subKegiatan.kegiatan.program',
                    'anggaran.subKegiatan.kegiatan',
                    'anggaran.subKegiatan',
                    'anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun.akun',
                    'anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun',
                    'anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun',
                    'anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun',
                    'anggaran.subRincianObyekAkun.rincianObyekAkun',
                    'anggaran.subRincianObyekAkun',
                    'anggaran'
                ]);


        if ($this->urusan_id) {
            $query->whereHas('anggaran.subKegiatan.kegiatan.program.subSkpd.skpd.urusanPelaksana.urusan',
                fn($q) => $q->where('id', $this->urusan_id));
        }

        if ($this->urusan_pelaksana_id) {
            $query->whereHas('anggaran.subKegiatan.kegiatan.program.subSkpd.skpd.urusanPelaksana',
                fn($q) => $q->where('id', $this->urusan_pelaksana_id));
        }

        if ($this->skpd_id) {
            $query->whereHas('anggaran.subKegiatan.kegiatan.program.subSkpd.skpd',
                fn($q) => $q->where('id', $this->skpd_id));
        }

        if ($this->sub_skpd_id) {
            $query->whereHas('anggaran.subKegiatan.kegiatan.program.subSkpd',
                fn($q) => $q->where('id', $this->sub_skpd_id));
        }

        if ($this->program_id) {
            $query->whereHas('anggaran.subKegiatan.kegiatan.program',
                fn($q) => $q->where('id', $this->program_id));
        }

        if ($this->kegiatan_id) {
            $query->whereHas('anggaran.subKegiatan.kegiatan',
                fn($q) => $q->where('id', $this->kegiatan_id));
        }

        if ($this->sub_kegiatan_id) {
            $query->whereHas('anggaran.subKegiatan',
                fn($q) => $q->where('id', $this->sub_kegiatan_id));
        }

        if ($this->akun_id) {
            $query->whereHas('anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun.akun',
                fn($q) => $q->where('id', $this->akun_id));
        }

        if ($this->kelompok_akun_id) {
            $query->whereHas('anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun',
                fn($q) => $q->where('id', $this->kelompok_akun_id));
        }

        if ($this->jenis_akun_id) {
            $query->whereHas('anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun',
                fn($q) => $q->where('id', $this->jenis_akun_id));
        }

        if ($this->obyek_akun_id) {
            $query->whereHas('anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun',
                fn($q) => $q->where('id', $this->obyek_akun_id));
        }

        if ($this->rincian_obyek_akun_id) {
            $query->whereHas('anggaran.subRincianObyekAkun.rincianObyekAkun',
                fn($q) => $q->where('id', $this->rincian_obyek_akun_id));
        }

        if ($this->sub_rincian_obyek_akun_id) {
            $query->whereHas('anggaran.subRincianObyekAkun',
                fn($q) => $q->where('id', $this->sub_rincian_obyek_akun_id));
        }

        if ($this->tahun) {
            $query->whereHas('anggaran',
                fn($q) => $q->where('tahun', $this->tahun));
        }

        $this->anggaran_id = $query->pluck('anggaran_id')->toArray();

        $this->realisasi = $query->when($this->anggaran_id, function ($q) {
            return $q->whereIn('anggaran_id', $this->anggaran_id);
        })->get();

        // Column Chart - Yearly Budget vs Realization
        $columnChartModel = (new ColumnChartModel())
            ->setTitle('Anggaran vs Realisasi per Tahun')
            ->setAnimated(true)
            ->withoutLegend();

        $years = $this->realisasi->pluck('tahun')->unique();

        foreach($years as $year) {
            $columnChartModel->addColumn(
            $year . ' Anggaran',
            $this->realisasi->where('tahun', $year)->sum('anggaran.rawNilaiAnggaran'),
            '#2E93fA'
            );
            $columnChartModel->addColumn(
            $year . ' Realisasi',
            $this->realisasi->where('tahun', $year)->sum('rawNilaiRealisasi'),
            '#66DA26'
            );
        }

        // Line Chart - Monthly Trends
        $lineChartModel = (new LineChartModel())
            ->setTitle('Trend Realisasi Bulanan')
            ->setAnimated(true);

        // Add monthly data points
        for ($i = 1; $i <= 12; $i++) {
            $lineChartModel->addPoint(
            date("F", mktime(0, 0, 0, $i, 1)),
            $this->realisasi->where('bulan', $i)->sum('rawNilaiRealisasi')
            );
        }

        // Pie Chart - Budget by SKPD
        $pieChartModel = (new PieChartModel())
            ->setTitle('Alokasi Anggaran per SKPD')
            ->setAnimated(true);

        $skpdData = $this->realisasi->groupBy('skpd_id')
            ->map(function($items) {
            return [
                'nama' => $items->first()->anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->nama,
                'total' => $items->sum('anggaran.rawNilaiAnggaran')
            ];
            });

        foreach($skpdData as $data) {
            $pieChartModel->addSlice(
                $data['nama'],
                $data['total'],
                '#' . substr(md5($data['nama']), 0, 6)
            );
        }

        // pie chart - Anggaran per tahun (akan muncul jika tahun tidak dipilih)
        $pieChartModel1 = (new PieChartModel())
            ->setTitle('Alokasi Anggaran per Tahun')
            ->setAnimated(true);

        $totalAnggaran = $this->realisasi->sum('anggaran.rawNilaiAnggaran');

        $totalRealisasi = $this->realisasi->sum('rawNilaiRealisasi');

        //dd($totalAnggaran, $totalRealisasi);

        $tahunData = $this->realisasi->groupBy('tahun')
            ->map(function($items) use ($totalAnggaran) {
            $total = $items->sum('anggaran.rawNilaiAnggaran');
            $percentage = ($total / $totalAnggaran) * 100;
            return [
            'tahun' => $items->first()->tahun,
            'total' => $total,
            'percentage' => $percentage
            ];
            });

        foreach($tahunData as $data) {
            $pieChartModel1->addSlice(
            $data['tahun'] . ' (' . number_format($data['percentage'], 2) . '%)',
            $data['total'],
            '#' . substr(md5($data['tahun']), 0, 6)
            );
        }


        return [
            'realisasi' => $this->realisasi,
            'anggaran' => $query->paginate(10),
            'urusan' => Urusan::orderBy('kode')->get(),
            'urusanPelaksana' => $this->urusan_id ? UrusanPelaksana::where('urusan_id', $this->urusan_id)->orderBy('kode')->get() : [],
            'skpd' => $this->urusan_pelaksana_id ? Skpd::where('urusan_pelaksana_id', $this->urusan_pelaksana_id)->orderBy('kode')->get() : [],
            'subSkpd' => $this->skpd_id ? SubSkpd::where('skpd_id', $this->skpd_id)->orderBy('kode')->get() : [],
            'program' => $this->sub_skpd_id ? Program::where('sub_skpd_id', $this->sub_skpd_id)->orderBy('kode')->get() : [],
            'kegiatan' => $this->program_id ? Kegiatan::where('program_id', $this->program_id)->orderBy('kode')->get() : [],
            'subKegiatan' => $this->kegiatan_id ? SubKegiatan::where('kegiatan_id', $this->kegiatan_id)->orderBy('kode')->get() : [],
            'akun' => $this->sub_kegiatan_id ? Akun::orderBy('kode')->get() : [],
            'kelompokAkun' => $this->akun_id ? KelompokAkun::where('akun_id', $this->akun_id)->orderBy('kode')->get() : [],
            'jenisAkun' => $this->kelompok_akun_id ? JenisAkun::where('kelompok_akun_id', $this->kelompok_akun_id)->orderBy('kode')->get() : [],
            'obyekAkun' => $this->jenis_akun_id ? ObyekAkun::where('jenis_akun_id', $this->jenis_akun_id)->orderBy('kode')->get() : [],
            'rincianObyekAkun' => $this->obyek_akun_id ? RincianObyekAkun::where('obyek_akun_id', $this->obyek_akun_id)->orderBy('kode')->get() : [],
            'subRincianObyekAkun' => $this->rincian_obyek_akun_id ? SubRincianObyekAkun::where('rincian_obyek_akun_id', $this->rincian_obyek_akun_id)->orderBy('kode')->get() : [],
            'tahun' => $this->tahun ? $this->tahun : [],
            'columnChartModel' => $columnChartModel,
            'lineChartModel' => $lineChartModel,
            'pieChartModel' => $pieChartModel,
            'pieChartModel1' => $pieChartModel1

        ];
    }



    public function resetFilters()
    {
        $this->reset([
            'urusan_id',
            'urusan_pelaksana_id',
            'skpd_id',
            'sub_skpd_id',
            'program_id',
            'kegiatan_id',
            'sub_kegiatan_id',
            'akun_id',
            'kelompok_akun_id',
            'jenis_akun_id',
            'obyek_akun_id',
            'rincian_obyek_akun_id',
            'sub_rincian_obyek_akun_id',
            'tahun'
        ]);
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
                            <select wire:model.live="urusan_id" class="form-select">
                                <option value="">Pilih Urusan</option>
                                @foreach($urusan as $u)
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
                            <select wire:model.live="urusan_pelaksana_id" class="form-select" @disabled(!$urusan_id)>
                                <option value="">Pilih Urusan Pelaksana</option>
                                @foreach($urusanPelaksana as $up)
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
                            <select wire:model.live="skpd_id" class="form-select" @disabled(!$urusan_pelaksana_id)>
                                <option value="">Pilih SKPD</option>
                                @foreach($skpd as $s)
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
                            <select wire:model.live="sub_skpd_id" class="form-select" @disabled(!$skpd_id)>
                                <option value="">Pilih Sub SKPD</option>
                                @foreach($subSkpd as $ss)
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
                            <select wire:model.live="program_id" class="form-select" @disabled(!$sub_skpd_id)>
                                <option value="">Pilih Program</option>
                                @foreach($program as $p)
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
                            <select wire:model.live="kegiatan_id" class="form-select" @disabled(!$program_id)>
                                <option value="">Pilih Kegiatan</option>
                                @foreach($kegiatan as $k)
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
                            <select wire:model.live="sub_kegiatan_id" class="form-select" @disabled(!$kegiatan_id)>
                                <option value="">Pilih Sub Kegiatan</option>
                                @foreach($subKegiatan as $sk)
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
                            <select wire:model.live="akun_id" class="form-select" @disabled(!$sub_kegiatan_id)>
                                <option value="">Pilih Akun</option>
                                @foreach($akun as $a)
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
                            <select wire:model.live="kelompok_akun_id" class="form-select" @disabled(!$akun_id)>
                                <option value="">Pilih Kelompok Akun</option>
                                @foreach($kelompokAkun as $ka)
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
                            <select wire:model.live="jenis_akun_id" class="form-select" @disabled(!$kelompok_akun_id)>
                                <option value="">Pilih Jenis Akun</option>
                                @foreach($jenisAkun as $ja)
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
                            <select wire:model.live="obyek_akun_id" class="form-select" @disabled(!$jenis_akun_id)>
                                <option value="">Pilih Obyek Akun</option>
                                @foreach($obyekAkun as $oa)
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
                            <select wire:model.live="rincian_obyek_akun_id" class="form-select" @disabled(!$obyek_akun_id)>
                                <option value="">Pilih Rincian Obyek Akun</option>
                                @foreach($rincianObyekAkun as $roa)
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
                            <select wire:model.live="sub_rincian_obyek_akun_id" class="form-select" @disabled(!$rincian_obyek_akun_id)>
                                <option value="">Pilih Sub Rincian Obyek Akun</option>
                                @foreach($subRincianObyekAkun as $sroa)
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
                            <select wire:model.live="tahun" class="form-select">
                                <option value="">Pilih Tahun</option>
                                @for($year = 2021; $year <= 2025; $year++) <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                            </select>
                            <div wire:loading wire:target="tahun" class="text-success">Memuat...</div>
                        </div>
                    </div>

                    <div class="col-md-6 align-content-end">
                        <div class="form-group mb-3 d-flex justify-content-end ">
                            {{-- <label>Cetak</label> --}}
                            <a href="#" class="btn btn-primary btn-sm btn-block p-3">
                                <span class="btn-label">
                                    <i class="fa fa-print"></i>
                                </span>
                                Cetak
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="accordion accordion-secondary">
        <div class="card">
            <div class="card-header" id="headingOne" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                <div class="span-title">
                    Grafik Laporan
                </div>
                <div class="span-mode"></div>
            </div>

            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="card card-round">
                                <div class="card-body">
                                    <div class="card-title fw-mediumbold">Anggaran vs Realisasi per Tahun</div>
                                    <div class="card-list"style="height: 500px;">
                                        <livewire:livewire-column-chart :column-chart-model="$columnChartModel" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card card-round">
                                <div class="card-body">
                                    <div class="card-title fw-mediumbold">Trend Realisasi Bulanan</div>
                                    <div class="card-list" style="height: 500px;">
                                        <livewire:livewire-line-chart :line-chart-model="$lineChartModel" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card card-round">
                                <div class="card-body">
                                    <div class="card-title fw-mediumbold">Alokasi Anggaran per SKPD</div>
                                    <div class="card-list" style="height: 500px;">
                                        <livewire:livewire-pie-chart :pie-chart-model="$pieChartModel" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card card-round">
                                <div class="card-body">
                                    <div class="card-title fw-mediumbold">Alokasi Anggaran per Tahun</div>
                                    <div class="card-list" style="height: 500px;">
                                        <livewire:livewire-pie-chart :pie-chart-model="$pieChartModel1" style="height: 500px;" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="card">
            <div class="card-header collapsed" id="headingTwo" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                <div class="span-title">
                    Tabel Laporan
                </div>
                <div class="span-mode"></div>
            </div>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
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
                                    <td>Rp {{ number_format($realisasi->sum(fn($item) => $item->anggaran->rawNilaiAnggaran), 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($realisasi->sum(fn($item) => $item->rawNilaiRealisasi), 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                                @foreach($anggaran as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>[{{ $item->anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->kode }}] {{ $item->anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->nama }}</td>
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

                    <div class="mt-3">
                        {{ $anggaran->links() }}
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="card">
            <div class="card-header collapsed" id="headingThree" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                <div class="span-title">
                    Lorem Ipsum #3
                </div>
                <div class="span-mode"></div>
            </div>
            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                <div class="card-body">
                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                </div>
            </div>
        </div>
    </div>

</div>
