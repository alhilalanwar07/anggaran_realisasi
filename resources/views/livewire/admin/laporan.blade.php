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
use App\Exports\RealisasiExport;


new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

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
    public $tahun = [];
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
    public $tahun_id;

    public $realisasiAda = false;

    public $colors = [], $labels = [], $data = []; // untuk chart

    public function with(): array
    {

        return [
            'urusanPelaksana' => $this->getDataUrusanPelaksana($this->urusan_id),
            'skpd' => $this->getDataSkpd($this->urusan_pelaksana_id),
            'subSkpd' => $this->getDataSubSkpd($this->skpd_id),
            'program' => $this->getDataProgram($this->sub_skpd_id),
            'kegiatan' => $this->getDataKegiatan($this->program_id),
            'subKegiatan' => $this->getDataSubKegiatan($this->kegiatan_id),
            'kelompokAkun' => $this->getDataKelompokAkun($this->akun_id),
            'jenisAkun' => $this->getDataJenisAkun($this->kelompok_akun_id),
            'obyekAkun' => $this->getDataObyekAkun($this->jenis_akun_id),
            'rincianObyekAkun' => $this->getDataRincianObyekAkun($this->obyek_akun_id),
            'subRincianObyekAkun' => $this->getDataSubRincianObyekAkun($this->rincian_obyek_akun_id),
            'tahun' => $this->getDataTahun(),
            // 'realisasi' => $this->getDataRealisasi(),
            
        ];
    }

    public function randomColors($count)
    {
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $colors[] = '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);
        }
        return $colors;
    }

    public function formatRupiah($angka)
    {
        $hasil_rupiah = "Rp " . number_format($angka, 2, ',', '.');
        return $hasil_rupiah;
    }

    public function downloadExcel()
    {
        return (new RealisasiExport($this->realisasi))->download('realisasi.xlsx');
    }

    public function mount(){
        $this->urusan = $this->getDataUrusan();
        $this->akun = $this->getDataAkun();
    }

    public function updatedUrusanId($value)
    {
        $this->urusanPelaksana = $this->getDataUrusanPelaksana($value);
        $this->reset(['urusan_pelaksana_id', 'skpd_id', 'sub_skpd_id', 'program_id', 'kegiatan_id', 'sub_kegiatan_id']);
    }

    public function updatedUrusanPelaksanaId($value)
    {
        $this->skpd = $this->getDataSkpd($value);
        $this->reset(['skpd_id', 'sub_skpd_id', 'program_id', 'kegiatan_id', 'sub_kegiatan_id']);
    }

    public function updatedSkpdId($value)
    {
        $this->subSkpd = $this->getDataSubSkpd($value);
        $this->reset(['sub_skpd_id', 'program_id', 'kegiatan_id', 'sub_kegiatan_id']);
    }

    public function updatedSubSkpdId($value)
    {
        $this->program = $this->getDataProgram($value);
        $this->reset(['program_id', 'kegiatan_id', 'sub_kegiatan_id']);
    }

    public function updatedProgramId($value)
    {
        $this->kegiatan = $this->getDataKegiatan($value);
        $this->reset(['kegiatan_id', 'sub_kegiatan_id']);
    }

    public function updatedKegiatanId($value)
    {
        $this->subKegiatan = $this->getDataSubKegiatan($value);
        $this->reset(['sub_kegiatan_id']);
    }

    public function updatedAkunId($value)
    {
        $this->kelompokAkun = $this->getDataKelompokAkun($value);
        $this->reset(['kelompok_akun_id', 'jenis_akun_id', 'obyek_akun_id', 'rincian_obyek_akun_id', 'sub_rincian_obyek_akun_id']);
    }

    public function updatedKelompokAkunId($value)
    {
        $this->jenisAkun = $this->getDataJenisAkun($value);
        $this->reset(['jenis_akun_id', 'obyek_akun_id', 'rincian_obyek_akun_id', 'sub_rincian_obyek_akun_id']);
    }

    public function updatedJenisAkunId($value)
    {
        $this->obyekAkun = $this->getDataObyekAkun($value);
        $this->reset(['obyek_akun_id', 'rincian_obyek_akun_id', 'sub_rincian_obyek_akun_id']);
    }

    public function updatedObyekAkunId($value)
    {
        $this->rincianObyekAkun = $this->getDataRincianObyekAkun($value);
        $this->reset(['rincian_obyek_akun_id', 'sub_rincian_obyek_akun_id']);
    }

    public function updatedRincianObyekAkunId($value)
    {
        $this->subRincianObyekAkun = $this->getDataSubRincianObyekAkun($value);
        $this->reset(['sub_rincian_obyek_akun_id']);
    }

    public function updatedTahunId($value)
    {
        $this->tahun = $this->getDataTahun();
    }

    public function getDataUrusan(){
        return Urusan::orderBy('kode')->get();
    }

    public function getDataUrusanPelaksana($urusan_id){
        return UrusanPelaksana::where('urusan_id', $urusan_id)->orderBy('kode')->get();
    }

    public function getDataSkpd($urusan_pelaksana_id){
        return Skpd::where('urusan_pelaksana_id', $urusan_pelaksana_id)->orderBy('kode')->get();
    }

    public function getDataSubSkpd($skpd_id){
        return SubSkpd::where('skpd_id', $skpd_id)->orderBy('kode')->get();
    }

    public function getDataProgram($sub_skpd_id){
        return Program::where('sub_skpd_id', $sub_skpd_id)->orderBy('kode')->get();
    }

    public function getDataKegiatan($program_id){
        return Kegiatan::where('program_id', $program_id)->orderBy('kode')->get();
    }

    public function getDataSubKegiatan($kegiatan_id){
        return SubKegiatan::where('kegiatan_id', $kegiatan_id)->orderBy('kode')->get();
    }

    public function getDataAkun(){
        return Akun::orderBy('kode')->get();
    }

    public function getDataKelompokAkun($akun_id){
        return KelompokAkun::where('akun_id', $akun_id)->orderBy('kode')->get();
    }

    public function getDataJenisAkun($kelompok_akun_id){
        return JenisAkun::where('kelompok_akun_id', $kelompok_akun_id)->orderBy('kode')->get();
    }

    public function getDataObyekAkun($jenis_akun_id){
        return ObyekAkun::where('jenis_akun_id', $jenis_akun_id)->orderBy('kode')->get();
    }

    public function getDataRincianObyekAkun($obyek_akun_id){
        return RincianObyekAkun::where('obyek_akun_id', $obyek_akun_id)->orderBy('kode')->get();
    }

    public function getDataSubRincianObyekAkun($rincian_obyek_akun_id){
        return SubRincianObyekAkun::where('rincian_obyek_akun_id', $rincian_obyek_akun_id)->orderBy('kode')->get();
    }

    public function getDataTahun(){
        return Realisasi::select('tahun')->distinct()->orderBy('tahun')->get();
    }

    public function tampilkanRealisasi(){
        $this->realisasiAda = true;

        $this->realisasi = $this->getDataRealisasi();

        dd($this->realisasi);
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
                            <select wire:model.live="urusan_pelaksana_id" class="form-select" wire:change="with">
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
                            <select wire:model.live="skpd_id" class="form-select" wire:change="with">
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
                            <select wire:model.live="sub_skpd_id" class="form-select" wire:change="with">
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
                            <select wire:model.live="program_id" class="form-select" wire:change="with">
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
                            <select wire:model.live="kegiatan_id" class="form-select" wire:change="with">
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
                            <select wire:model.live="sub_kegiatan_id" class="form-select" wire:change="with">
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
                            <select wire:model.live="akun_id" class="form-select" wire:change="with">
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
                            <select wire:model.live="kelompok_akun_id" class="form-select" wire:change="with">
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
                            <select wire:model.live="jenis_akun_id" class="form-select" wire:change="with">
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
                            <select wire:model.live="obyek_akun_id" class="form-select" wire:change="with">
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
                            <select wire:model.live="rincian_obyek_akun_id" class="form-select" wire:change="with">
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
                            <select wire:model.live="sub_rincian_obyek_akun_id" class="form-select" wire:change="with">
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
                            <select wire:model.live="tahun_id" class="form-select" wire:change="with">
                                <option value="">Pilih Tahun</option>
                                @for($year = 2021; $year <= 2025; $year++) <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                            </select>
                            <div wire:loading wire:target="tahun_id" class="text-success">Memuat...</div>
                        </div>
                    </div>

                    <div class="col-md-6 align-content-end">
                        <div class="form-group mb-3 d-flex justify-content-end gap-2">
                            {{-- <label>Cetak</label> --}}
                            <button wire:click="downloadExcel" class="btn btn-success btn-sm btn-block p-3">
                                <span class="btn-label">
                                    <i class="fa fa-print"></i>
                                </span>
                                Cetak Excel
                                <div wire:loading wire:target="downloadExcel" class="text-success">Memuat...</div>
                            </button>
                            <!-- tombol Tampilkan Grafik, Tombol Tampilkan Tabel -->
                            <button wire:click="tampilkanRealisasi" class="btn btn-primary btn-sm btn-block p-3">
                                <span class="btn-label">
                                    <i class="fa fa-table"></i>
                                </span>
                                Tampilkan Tabel
                                <div wire:loading wire:target="tampilkanRealisasi" class="text-success">Memuat...</div>
                            </button>
                            <button wire:click="downloadExcel" class="btn btn-secondary btn-sm btn-block p-3">
                                <span class="btn-label">
                                    <i class="fa fa-chart-bar"></i>
                                </span>
                                Tampilkan Grafik
                                <div wire:loading wire:target="downloadExcel" class="text-success">Memuat...</div>
                            </button>
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


            {{-- <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="card card-round">
                                <div class="card-body">
                                    <div class="card-title fw-mediumbold">Anggaran vs Realisasi per Tahun</div>
                                    <div class="card-list"style="height: 500px;">
                                        <livewire:livewire-column-chart :column-chart-model="$columnChartModel" wire:poll.5000ms />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card card-round">
                                <div class="card-body">
                                    <div class="card-title fw-mediumbold">Trend Realisasi Bulanan</div>
                                    <div class="card-list" style="height: 500px;">
                                        <livewire:livewire-line-chart :line-chart-model="$lineChartModel" wire:poll.5000ms />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-round">
                                <div class="card-body">
                                    <div class="card-title fw-mediumbold">Alokasi Anggaran per SKPD</div>
                                    <div class="card-list" style="height: 500px;">
                                        <livewire:livewire-pie-chart :pie-chart-model="$pieChartModel" wire:poll.5000ms />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-round">
                                <div class="card-body">
                                    <div class="card-title fw-mediumbold">Alokasi Anggaran per Tahun</div>
                                    <div class="card-list" style="height: 500px;">
                                        <livewire:livewire-pie-chart :pie-chart-model="$pieChartModel1" wire:poll.5000ms />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
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
    </div>

</div>