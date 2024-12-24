<?php

use Livewire\Volt\Component;
use App\Models\Realisasi;
use function Livewire\Volt\{computed, state};
use Livewire\WithPagination;
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
use Livewire\WithFileUploads;
use App\Imports\ImportDataRealisasi;
use Maatwebsite\Excel\Facades\Excel;


new class extends Component {
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';
    public $paginate = 10;
    public $search = '';
    public $progress = 0; // Untuk melacak progres unggahan
    public $file; // Properti untuk file

    public $nilai_anggaran, $nilai_realisasi, $tahun, $anggaran_id;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'realisasi' => Realisasi::with([
                            'anggaran.subKegiatan.kegiatan.program.subSkpd.skpd.urusanPelaksana',
                            'anggaran.subKegiatan.kegiatan.program.subSkpd.skpd',
                            'anggaran.subKegiatan.kegiatan.program.subSkpd',
                            'anggaran.subKegiatan.kegiatan.program',
                            'anggaran.subKegiatan.kegiatan',
                            'anggaran.subKegiatan',
                            'anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun',
                            'anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun',
                            'anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun',
                            'anggaran.subRincianObyekAkun.rincianObyekAkun',
                            'anggaran.subRincianObyekAkun'
                        ])
                        ->where('tahun', 'like', '%' . $this->tahun . '%')
                        ->where(function($query) {
                            $query->where('nilai_realisasi', 'like', '%' . $this->search . '%')
                                ->orWhereHas('anggaran.subKegiatan.kegiatan.program.subSkpd.skpd.urusanPelaksana', function($query) {
                                    $query->where('nama', 'like', '%' . $this->search . '%');
                                })
                                ->orWhereHas('anggaran.subKegiatan.kegiatan.program.subSkpd.skpd', function($query) {
                                    $query->where('nama', 'like', '%' . $this->search . '%');
                                })
                                ->orWhereHas('anggaran.subKegiatan.kegiatan.program.subSkpd', function($query) {
                                    $query->where('nama', 'like', '%' . $this->search . '%');
                                })
                                ->orWhereHas('anggaran.subKegiatan.kegiatan.program', function($query) {
                                    $query->where('nama', 'like', '%' . $this->search . '%');
                                })
                                ->orWhereHas('anggaran.subKegiatan.kegiatan', function($query) {
                                    $query->where('nama', 'like', '%' . $this->search . '%');
                                })
                                ->orWhereHas('anggaran.subKegiatan', function($query) {
                                    $query->where('nama', 'like', '%' . $this->search . '%');
                                })
                                ->orWhereHas('anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun', function($query) {
                                    $query->where('nama', 'like', '%' . $this->search . '%');
                                })
                                ->orWhereHas('anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun', function($query) {
                                    $query->where('nama', 'like', '%' . $this->search . '%');
                                })
                                ->orWhereHas('anggaran.subRincianObyekAkun.rincianObyekAkun.obyekAkun', function($query) {
                                    $query->where('nama', 'like', '%' . $this->search . '%');
                                })
                                ->orWhereHas('anggaran.subRincianObyekAkun.rincianObyekAkun', function($query) {
                                    $query->where('nama', 'like', '%' . $this->search . '%');
                                })
                                ->orWhereHas('anggaran.subRincianObyekAkun', function($query) {
                                    $query->where('nama', 'like', '%' . $this->search . '%');
                                });
                        })
                        ->orderBy('nilai_realisasi', 'desc')
                        ->paginate($this->paginate),
            'total' => Realisasi::sumNilaiRealisasi()
        ];
    }

    public function importExcel()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx'
        ], [
            'file.required' => 'File tidak boleh kosong',
            'file.mimes' => 'File harus berformat xlsx'
        ]);

        // toast error jika terjadi kesalahan


       try {
            $file = $this->file;
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('excel', $fileName);

            $path = storage_path('app/private/excel/' . $fileName);

            Excel::import(new ImportDataRealisasi, $path);

            $this->progress = 100;
            $this->dispatch('progressUpdated', $this->progress);
            $this->reset(['file']);
            $this->dispatch('tambahAlertToast');
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast', $e->getMessage());
        }
    }

    public function close()
    {
        $this->reset(['file']);
    }

}; ?>

<div>
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Realisasi
                        <div class="badge badge-primary">{{ number_format($total, 0, ',', '.') }}</div>
                    </div>
                    <div class="card-tools">
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalImport">
                            <i class="fa fa-file-excel"></i> &nbsp;Import Excel
                        </button>
                        <button class="btn btn-info btn-sm" wire:click="exportExcel">
                            <i class="fa fa-download"></i> &nbsp;Export Excel
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="m-2">
                    <div class="d-flex mb-3 justify-content-between gap-2">
                        <div class="d-flex mb-3 justify-content-between gap-2">
                            <select wire:model.live="paginate" class="form-select w-auto">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="500">500</option>
                            </select>
                            <input wire:model.live="search" type="text" class="form-control w-auto" placeholder="Cari...">
                        </div>
                        <div class="">
                            <select wire:model.live="tahun" class="form-control w-auto">
                                <option value="">Semua Tahun</option>
                                @for($i = date('Y'); $i >= 2020; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>SKPD</th>
                                <th>Kegiatan</th>
                                <th>Akun</th>
                                <th>Nilai Realisasi</th>
                                <th>Tahun</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($realisasi as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge badge-primary">
                                        {{ $item->anggaran_id && $item->anggaran->subKegiatan && $item->anggaran->subKegiatan->kegiatan && $item->anggaran->subKegiatan->kegiatan->program && $item->anggaran->subKegiatan->kegiatan->program->subSkpd && $item->anggaran->subKegiatan->kegiatan->program->subSkpd->skpd && $item->anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->urusanPelaksana ? $item->anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->urusanPelaksana->kode : 'null' }}.{{ $item->anggaran_id && $item->anggaran->subKegiatan && $item->anggaran->subKegiatan->kegiatan && $item->anggaran->subKegiatan->kegiatan->program && $item->anggaran->subKegiatan->kegiatan->program->subSkpd && $item->anggaran->subKegiatan->kegiatan->program->subSkpd->skpd ? $item->anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->kode : 'null' }}.{{ $item->anggaran_id && $item->anggaran->subKegiatan && $item->anggaran->subKegiatan->kegiatan && $item->anggaran->subKegiatan->kegiatan->program && $item->anggaran->subKegiatan->kegiatan->program->subSkpd ? $item->anggaran->subKegiatan->kegiatan->program->subSkpd->kode : 'null' }}<br>
                                    </span><br>
                                    {{ $item->anggaran_id && $item->anggaran->subKegiatan && $item->anggaran->subKegiatan->kegiatan && $item->anggaran->subKegiatan->kegiatan->program && $item->anggaran->subKegiatan->kegiatan->program->subSkpd ? $item->anggaran->subKegiatan->kegiatan->program->subSkpd->nama : 'null' }}
                                </td>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ $item->anggaran_id && $item->anggaran->subKegiatan && $item->anggaran->subKegiatan->kegiatan && $item->anggaran->subKegiatan->kegiatan->program ? $item->anggaran->subKegiatan->kegiatan->program->kode : 'null' }}.{{ $item->anggaran_id && $item->anggaran->subKegiatan && $item->anggaran->subKegiatan->kegiatan ? $item->anggaran->subKegiatan->kegiatan->kode : 'null' }}.{{ $item->anggaran_id && $item->anggaran->subKegiatan ? $item->anggaran->subKegiatan->kode : 'null' }}<br>
                                    </span><br>{{ $item->anggaran_id && $item->anggaran->subKegiatan ? $item->anggaran->subKegiatan->nama : 'null' }}
                                </td>
                                <td>
                                    <span class="badge badge-danger">
                                        {{ $item->anggaran_id && $item->anggaran->subRincianObyekAkun && $item->anggaran->subRincianObyekAkun->rincianObyekAkun && $item->anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun && $item->anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun && $item->anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun->kelompokAkun ? $item->anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun->kelompokAkun->kode : 'null' }}.{{ $item->anggaran_id && $item->anggaran->subRincianObyekAkun && $item->anggaran->subRincianObyekAkun->rincianObyekAkun && $item->anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun && $item->anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun ? $item->anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun->kode : 'null' }}.{{ $item->anggaran_id && $item->anggaran->subRincianObyekAkun && $item->anggaran->subRincianObyekAkun->rincianObyekAkun && $item->anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun ? $item->anggaran->subRincianObyekAkun->rincianObyekAkun->obyekAkun->kode : 'null' }}.{{ $item->anggaran_id && $item->anggaran->subRincianObyekAkun && $item->anggaran->subRincianObyekAkun->rincianObyekAkun ? $item->anggaran->subRincianObyekAkun->rincianObyekAkun->kode : 'null' }}.{{ $item->anggaran_id && $item->anggaran->subRincianObyekAkun ? $item->anggaran->subRincianObyekAkun->kode : 'null' }}<br>
                                    </span><br>{{ $item->anggaran_id && $item->anggaran->subRincianObyekAkun ? $item->anggaran->subRincianObyekAkun->nama : 'null' }}
                                </td>
                                <td>{{ $item->nilai_realisasi }}</td>
                                <td>{{ $item->tahun }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm mb-1" wire:click="edit({{ $item->id }})" data-bs-toggle="modal" data-bs-target="#modalEdit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm mb-1" wire:click="confirmDelete({{ $item->id }})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="justify-content-between">
                    {{ $realisasi->links() }}
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalImport" tabindex="-1" wire:ignore.self data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data Realisasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit.prevent="importExcel" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="file">File Excel</label>
                            <div x-data="{ uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true" x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-cancel="uploading = false" x-on:livewire-upload-error="uploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">
                                <input type="file" class="form-control  @error('file') is-invalid @enderror" id="file" wire:model="file" wire:loading.attr="disabled" accept=".xlsx">
                                <div x-show="uploading" class="progress mt-3">
                                    <div class="progress-bar" role="progressbar" :style="{ width: progress + '%' }" aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100" x-text="progress + '%'"></div>
                                </div>
                            </div>
                            @error('file') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="close">Tutup</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('script')
    <script>
        document.addEventListener('livewire:navigated', function() {

            Livewire.on('confirmDelete', (id) => {
                swal({
                    title: "Apakah Anda yakin?"
                    , text: "Data yang dihapus tidak dapat dikembalikan!"
                    , icon: "warning"
                    , buttons: {
                        cancel: {
                            text: "Batal"
                            , value: null
                            , visible: true
                            , className: "btn btn-secondary"
                            , closeModal: true
                        , }
                        , confirm: {
                            text: "Hapus"
                            , value: true
                            , visible: true
                            , className: "btn btn-danger"
                            , closeModal: true
                        }
                    }
                }).then((willDelete) => {
                    if (willDelete) {
                        @this.call('delete', id);
                    }
                });
            });
        });

    </script>
    @endpush
    <livewire:_alert />
</div>
