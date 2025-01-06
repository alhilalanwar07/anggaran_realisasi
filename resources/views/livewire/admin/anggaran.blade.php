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
use App\Exports\ExportData;
use App\Models\Realisasi;


new class extends Component {
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';
    public $paginate = 10;
    public $search = '';
    public $progress = 0; // Untuk melacak progres unggahan
    public $file; // Properti untuk file

    public $nilai_anggaran, $nilai_realisasi, $anggaran_id;

    public $tahun = '';

    public $nilai_anggaran_edit, $skpd_edit, $kegiatan_edit, $akun_edit, $tahun_edit;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'anggaran' => Anggaran::with([
                            'subKegiatan.kegiatan.program.subSkpd.skpd.urusanPelaksana',
                            'subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun',
                            'subRincianObyekAkun.rincianObyekAkun.obyekAkun.jenisAkun',
                            'subRincianObyekAkun.rincianObyekAkun.obyekAkun',
                            'subRincianObyekAkun.rincianObyekAkun',
                            'subRincianObyekAkun',
                            'subKegiatan.kegiatan.program.subSkpd.skpd'
                        ])
                        ->where('tahun', 'like', '%' . $this->tahun . '%')
                        ->where(function($query) {
                            $query->whereHas('subKegiatan', function($query) {
                                $query->where('nama', 'like', '%' . $this->search . '%');
                            })
                            ->orWhereHas('subKegiatan.kegiatan.program.subSkpd', function($query) {
                                $query->where('nama', 'like', '%' . $this->search . '%');
                            })
                            ->orWhereHas('subKegiatan.kegiatan.program', function($query) {
                                $query->where('nama', 'like', '%' . $this->search . '%');
                            })
                            ->orWhereHas('subKegiatan.kegiatan', function($query) {
                                $query->where('nama', 'like', '%' . $this->search . '%');
                            })
                            ->orWhereHas('subRincianObyekAkun', function($query) {
                                $query->where('nama', 'like', '%' . $this->search . '%');
                            })
                            ->orWhereHas('subKegiatan.kegiatan.program.subSkpd.skpd', function($query) {
                                $query->where('nama', 'like', '%' . $this->search . '%');
                            })
                            ->orWhereHas('subKegiatan.kegiatan.program.subSkpd.skpd.urusanPelaksana', function($query) {
                                $query->where('nama', 'like', '%' . $this->search . '%');
                            });
                        })
                        ->orderBy('nilai_anggaran', 'desc')
                        ->paginate($this->paginate),
            'total' => Anggaran::sumNilaiAnggaran()
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

    public function exportExcel()
    {
        $this->validate([
            'tahun' => 'required'
        ], [
            'tahun.required' => 'Pilih tahun sebelum export'
        ]);
        try {
            return Excel::download(new ExportData($this->tahun), 'anggaran-'.$this->tahun.'.xlsx');
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast', $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->dispatch('confirmDelete', $id);
    }

    public function delete($id)
    {
        $anggaran = Anggaran::find($id);
        if (Realisasi::where('anggaran_id', $id)->exists()) {
            $this->dispatch('errorAlertToast',[
                'message' => 'Data tidak dapat dihapus karena sudah direalisasi'
            ]);
            return;
        }
        try {
            $anggaran->delete();
            $this->dispatch('deleteAlertToast');
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $anggaran = Anggaran::find($id);
        $this->anggaran_id = $anggaran->id;
        $this->nilai_anggaran_edit = $anggaran->rawNilaiAnggaran;
        $this->skpd_edit = $anggaran->subKegiatan->kegiatan->program->subSkpd->skpd->nama;
        $this->kegiatan_edit = $anggaran->subKegiatan->kegiatan->program->nama;
        $this->akun_edit = $anggaran->subRincianObyekAkun->nama;
        $this->tahun_edit = $anggaran->tahun;
    }

    public function update()
    {
        $this->validate([
            'nilai_anggaran_edit' => 'required|numeric'
        ], [
            'nilai_anggaran_edit.required' => 'Nilai anggaran tidak boleh kosong',
            'nilai_anggaran_edit.numeric' => 'Nilai anggaran harus berupa angka'
        ]);

        try {
            $anggaran = Anggaran::find($this->anggaran_id);
            $anggaran->update([
                'nilai_anggaran' => $this->nilai_anggaran_edit
            ]);
            $this->dispatch('updateAlertToast');
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast', $e->getMessage());
        }
    }

    // download file anggaran_excel_template.xlsx from storage
    public function downloadTemplate()
    {
        $path = storage_path('app/public/anggaran_excel_template.xlsx');
        return response()->download($path);
    }
}; ?>

<div>
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Anggaran
                        <div class="badge badge-primary">{{ number_format($total, 0, ',', '.') }}</div>
                    </div>
                    <div class="card-tools">
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalImport">
                            <i class="fa fa-file-excel"></i> &nbsp;Import Excel
                        </button>
                        <button class="btn btn-info btn-sm" wire:click="exportExcel">
                            <i class="fa fa-download"></i> &nbsp;Export Excel
                                <div wire:loading wire:target="exportExcel" class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
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
                            <select wire:model.live="tahun" class="form-control w-auto @error('tahun') is-invalid @enderror">
                                <option value="">Semua Tahun</option>
                                @for($i = date('Y'); $i >= 2020; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            @error('tahun') <span class="error text-danger">{{ $message }}</span> @enderror
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
                    {{ $anggaran->links() }}
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalImport" tabindex="-1" wire:ignore.self data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data Anggaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit.prevent="importExcel" enctype="multipart/form-data">
                    <div class="modal-body">
                        <!-- petunjuk upload -->
                        <div class="alert alert-info shadow-none bg-light" style="border: 2px dashed #17a2b8;">
                            <h4 class="alert-heading text-undeline badge bg-dark text-light">Petunjuk Upload</h4>
                            <p class="mb-0">1. Download  <a href="#" wire:click.prevent="downloadTemplate" class="text-underline"><u>Template Excel</u></a>
                                <br> 2. Isi data sesuai dengan template excel yang telah di download
                                <br> 3. Upload file excel yang telah di isi dibawah.
                            </p>
                        </div>

                        <div class="form-group mb-3">
                            <label for="file">File Excel</label>
                            <div x-data="{ uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true" x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-cancel="uploading = false" x-on:livewire-upload-error="uploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">
                                <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" wire:model="file" wire:loading.attr="disabled" accept=".xlsx">
                                <div x-show="uploading" class="progress mt-3">
                                    <div class="progress-bar" role="progressbar" :style="{ width: progress + '%' }" aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100" x-text="progress + '%'"></div>
                                </div>
                            </div>
                            @error('file') <span class="error text-danger ">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="close">Tutup</button>
                        <button type="submit" class="btn btn-primary">Import
                            <div wire:loading wire:target="importExcel" class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- modal udpate nilai anggaran --}}
    <div class="modal fade" id="modalEdit" tabindex="-1" wire:ignore.self data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Nilai Anggaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit.prevent="update">
                    <div class="modal-body row">
                        {{-- skpd, kegiatan, akun, tahun= disable. nilai_anggaran = bisa di ubah --}}
                        <div class="form-group mb-3 col-md-6">
                            <label for="skpd_edit">SKPD</label>
                            <textarea class="form-control" id="skpd_edit" wire:model="skpd_edit" disabled rows="3"></textarea>
                        </div>
                        <div class="form-group mb-3 col-md-6">
                            <label for="kegiatan_edit">Kegiatan</label>
                            <textarea class="form-control" id="kegiatan_edit" wire:model="kegiatan_edit" disabled rows="3"></textarea>
                        </div>
                        <div class="form-group mb-3 col-md-6">
                            <label for="akun_edit">Akun</label>
                            <textarea class="form-control" id="akun_edit" wire:model="akun_edit" disabled rows="3"></textarea>
                        </div>
                        <div class="form-group mb-3 col-md-6">
                            <label for="tahun_edit">Tahun</label>
                            <textarea class="form-control" id="tahun_edit" wire:model="tahun_edit" disabled rows="3"></textarea>
                        </div>
                        <div class="form-group mb-3 col-md-12">
                            <label for="nilai_anggaran_edit">Nilai Anggaran</label>
                            <input type="text" class="form-control @error('nilai_anggaran_edit') is-invalid @enderror" id="nilai_anggaran_edit" wire:model="nilai_anggaran_edit">
                            @error('nilai_anggaran_edit') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="close">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan
                            <div wire:loading wire:target="update" class="spinner-border text-light spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </button>
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
