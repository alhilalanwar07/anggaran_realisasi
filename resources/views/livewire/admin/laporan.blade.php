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
use function Livewire\Volt\{computed, state};
use Livewire\WithPagination;

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

    public function with(): array
    {
        $query = Anggaran::query()
            ->with([
                'subKegiatan.kegiatan.program.subSkpd.skpd.urusanPelaksana.urusan',
                'subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun.akun'
            ]);

        if ($this->urusan_id) {
            $query->whereHas('subKegiatan.kegiatan.program.subSkpd.skpd.urusanPelaksana.urusan',
                fn($q) => $q->where('id', $this->urusan_id));
        }

        if ($this->urusan_pelaksana_id) {
            $query->whereHas('subKegiatan.kegiatan.program.subSkpd.skpd.urusanPelaksana',
                fn($q) => $q->where('id', $this->urusan_pelaksana_id));
        }

        if ($this->skpd_id) {
            $query->whereHas('subKegiatan.kegiatan.program.subSkpd.skpd',
                fn($q) => $q->where('id', $this->skpd_id));
        }

        if ($this->sub_skpd_id) {
            $query->whereHas('subKegiatan.kegiatan.program.subSkpd',
                fn($q) => $q->where('id', $this->sub_skpd_id));
        }

        if ($this->program_id) {
            $query->whereHas('subKegiatan.kegiatan.program',
                fn($q) => $q->where('id', $this->program_id));
        }

        if ($this->kegiatan_id) {
            $query->whereHas('subKegiatan.kegiatan',
                fn($q) => $q->where('id', $this->kegiatan_id));
        }

        if ($this->sub_kegiatan_id) {
            $query->whereHas('subKegiatan',
                fn($q) => $q->where('id', $this->sub_kegiatan_id));
        }

        if ($this->akun_id) {
            $query->whereHas('subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun.akun',
                fn($q) => $q->where('id', $this->akun_id));
        }

        if ($this->kelompok_akun_id) {
            $query->whereHas('subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun',
                fn($q) => $q->where('id', $this->kelompok_akun_id));
        }

        if ($this->jenis_akun_id) {
            $query->whereHas('subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun',
                fn($q) => $q->where('id', $this->jenis_akun_id));
        }

        if ($this->obyek_akun_id) {
            $query->whereHas('subRincianObyekAkun.rincianObyekAkun.obyekAkun',
                fn($q) => $q->where('id', $this->obyek_akun_id));
        }

        if ($this->rincian_obyek_akun_id) {
            $query->whereHas('subRincianObyekAkun.rincianObyekAkun',
                fn($q) => $q->where('id', $this->rincian_obyek_akun_id));
        }

        if ($this->sub_rincian_obyek_akun_id) {
            $query->whereHas('subRincianObyekAkun',
                fn($q) => $q->where('id', $this->sub_rincian_obyek_akun_id));
        }

        return [
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
            'sub_rincian_obyek_akun_id'
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
                        <a href="#" class="btn btn-primary btn-round btn-sm">
                            <span class="btn-label">
                                <i class="fa fa-print"></i>
                            </span>
                            Cetak
                        </a>
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
                        </div>
                    </div>

                    <!-- Add more filters here -->
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
                    </div>
                </div>
                </div>

                <!-- Table to display filtered data -->
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
                            @foreach($anggaran as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>[{{ $item->subKegiatan->kegiatan->program->subSkpd->skpd->kode }}] {{ $item->subKegiatan->kegiatan->program->subSkpd->skpd->nama }}</td>
                                <td>[{{ $item->subKegiatan->kegiatan->kode }}] {{ $item->subKegiatan->kegiatan->nama }}</td>
                                <td>[{{ $item->subRincianObyekAkun->kode }}] {{ $item->subRincianObyekAkun->nama }}</td>
                                <td>{{ $item->nilai_anggaran }}</td>
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
    </div>
</div>
