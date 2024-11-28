<?php

use Livewire\Volt\Component;
use App\Models\Anggaran;
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
use App\Imports\ImportData;
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
            'anggaran' => Anggaran::where('nilai_anggaran', 'like', '%' . $this->search . '%')
                        ->orderBy('tahun', 'desc')
                        ->paginate($this->paginate)
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

        $file = $this->file;
        $fileName = time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('excel', $fileName);

        $path = storage_path('app/private/excel/' . $fileName);

        Excel::import(new ImportData, $path);

        $this->progress = 100;
        $this->dispatch('progressUpdated', $this->progress);
        $this->reset(['file']);
        $this->dispatch('tambahAlertToast');


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
                    <div class="card-title">Anggaran</div>
                    <div class="card-tools">
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalImport">
                            <i class="fa fa-file-excel"></i> &nbsp;Import Excel
                        </button>
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="fa fa-plus"></i> Tambah Anggaran
                        </button>
                    </div>
                </div>
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
                                <th>Tahun</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($anggaran as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                <span class="badge badge-primary">
                                    {{ $item->subKegiatan->kegiatan->program->subSkpd->skpd->urusanPelaksana->kode }}.{{ $item->subKegiatan->kegiatan->program->subSkpd->skpd->kode }}.{{ $item->subKegiatan->kegiatan->program->subSkpd->kode }}<br>
                                </span><br>
                                    {{ $item->subKegiatan->kegiatan->program->subSkpd->nama }}
                                </td>
                                <td>
                                <span class="badge badge-secondary">
                                    {{ $item->subKegiatan->kegiatan->program->kode }}.{{ $item->subKegiatan->kegiatan->kode }}.{{ $item->subKegiatan->kode }}<br>
                                </span><br>{{ $item->subKegiatan->nama }}
                                </td>
                                <td>
                                <span class="badge badge-danger">
                                    {{ $item->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun->kelompokAkun->kode }}.{{ $item->subRincianObyekAkun->rincianObyekAkun->obyekAkun->jenisAkun->kode }}.{{ $item->subRincianObyekAkun->rincianObyekAkun->obyekAkun->kode }}.{{ $item->subRincianObyekAkun->rincianObyekAkun->kode }}.{{ $item->subRincianObyekAkun->kode }}<br>
                                </span><br>{{ $item->subRincianObyekAkun->nama }}
                                </td>
                                <td>{{ $item->nilai_anggaran }}</td>
                                <td>{{ $item->tahun }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" wire:click="edit({{ $item->id }})" data-bs-toggle="modal" data-bs-target="#modalEdit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" wire:click="confirmDelete({{ $item->id }})">
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
                    {{ $anggaran->links() }}
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalImport" tabindex="-1" wire:ignore.self data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data Anggaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit.prevent="importExcel" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="file">File Excel</label>
                            <div x-data="{ uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true" x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-cancel="uploading = false" x-on:livewire-upload-error="uploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">
                                <input type="file" class="form-control" id="file" wire:model="file" wire:loading.attr="disabled" accept=".xlsx">
                                <div x-show="uploading" class="progress mt-3">
                                    <div class="progress-bar" role="progressbar" :style="{ width: progress + '%' }" aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100" x-text="progress + '%'"></div>
                                </div>
                            </div>
                            @error('file') <span class="error">{{ $message }}</span> @enderror
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
