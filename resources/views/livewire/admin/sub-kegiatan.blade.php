<?php

use Livewire\Volt\Component;
use App\Models\SubKegiatan;
use App\Models\Kegiatan;
use function Livewire\Volt\{computed, state};
use Livewire\WithPagination;
use App\Models\Anggaran;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $paginate = 10;
    public $search = '';

    public $nama, $kode, $kegiatan_id, $sub_kegiatan_id;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'subKegiatan' => SubKegiatan::with('kegiatan.program.subSkpd.skpd.urusanPelaksana')
                        ->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('kode', 'like', '%' . $this->search . '%')
                        ->paginate($this->paginate),
            'kegiatan' => Kegiatan::with('program.subSkpd.skpd.urusanPelaksana')
                        ->orderBy('kode', 'asc')
                        ->get()
        ];
    }

    public function store()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required|unique:sub_kegiatans,kode',
            'kegiatan_id' => 'required'
        ], [
            'nama.required' => 'Nama tidak boleh kosong',
            'kode.required' => 'Kode tidak boleh kosong',
            'kode.unique' => 'Kode sudah digunakan',
            'kegiatan_id.required' => 'Kegiatan harus dipilih'
        ]);

        SubKegiatan::create([
            'nama' => $this->nama,
            'kode' => $this->kode,
            'kegiatan_id' => $this->kegiatan_id
        ]);

        $this->reset(['nama', 'kode', 'kegiatan_id']);
        $this->dispatch('tambahAlertToast');
    }

    public function edit($id)
    {
        $subKegiatan = SubKegiatan::find($id);
        $this->sub_kegiatan_id = $subKegiatan->id;
        $this->nama = $subKegiatan->nama;
        $this->kode = $subKegiatan->kode;
        $this->kegiatan_id = $subKegiatan->kegiatan_id;
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required|unique:sub_kegiatans,kode,' . $this->sub_kegiatan_id,
            'kegiatan_id' => 'required'
        ]);

        try {
            $subKegiatan = SubKegiatan::find($this->sub_kegiatan_id);
            $subKegiatan->update([
                'nama' => $this->nama,
                'kode' => $this->kode,
                'kegiatan_id' => $this->kegiatan_id
            ]);

            $this->reset(['nama', 'kode', 'kegiatan_id', 'sub_kegiatan_id']);
            $this->dispatch('updateAlertToast');
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast');
        }
    }

    public function close()
    {
        $this->reset(['nama', 'kode', 'kegiatan_id', 'sub_kegiatan_id']);
    }

    public function confirmDelete($id)
    {
        $this->sub_kegiatan_id = $id;
        $this->dispatch('confirmDelete', $id);
    }

    public function delete($id)
    {
        try {
            $subKegiatan = SubKegiatan::find($id)->first();
            if ($subKegiatan) {
                if (Anggaran::where('sub_kegiatan_id', $id)->count() > 0) {
                    $this->dispatch('errorAlertToast', 'Sub Kegiatan tidak bisa dihapus karena sudah digunakan pada anggaran');
                } else {
                    $subKegiatan->delete();
                    $this->dispatch('deleteAlertToast');
                }
            }
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast', 'Terjadi kesalahan saat menghapus Sub Kegiatan');
        }
    }
}; ?>

<div>
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Sub Kegiatan</div>
                    <div class="card-tools">
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="fa fa-plus"></i> Tambah Sub Kegiatan
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
                                <th>Kegiatan</th>
                                <th>Kode/Nama Sub Kegiatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subKegiatan as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>[{{ $item->kegiatan->program->subSkpd->skpd->urusanPelaksana->kode }}.{{ $item->kegiatan->program->subSkpd->skpd->kode }}.{{ $item->kegiatan->program->subSkpd->kode }}.{{ $item->kegiatan->program->kode }}.{{ $item->kegiatan->kode }}] {{ $item->kegiatan->nama }}</td>
                                <td>[{{ $item->kode }}] {{ $item->nama }}</td>
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
                <div class=" justify-content-between">
                    {{ $subKegiatan->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" wire:ignore.self  data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Sub Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kegiatan</label>
                            <select class="form-control" wire:model="kegiatan_id">
                                <option value="">Pilih Kegiatan</option>
                                @foreach($kegiatan as $k)
                                    <option value="{{ $k->id }}">[{{ $k->kode }}] {{ $k->nama }}</option>
                                @endforeach
                            </select>
                            @error('kegiatan_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" class="form-control" wire:model="nama">
                            @error('nama') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kode</label>
                            <input type="text" class="form-control" wire:model="kode">
                            @error('kode') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="close">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1" wire:ignore.self  data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Sub Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kegiatan</label>
                            <select class="form-control" wire:model="kegiatan_id">
                                <option value="">Pilih Kegiatan</option>
                                @foreach($kegiatan as $k)
                                    <option value="{{ $k->id }}">[{{ $k->kode }}] {{ $k->nama }}</option>
                                @endforeach
                            </select>
                            @error('kegiatan_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" class="form-control" wire:model="nama">
                            @error('nama') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kode</label>
                            <input type="text" class="form-control" wire:model="kode">
                            @error('kode') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="close">Tutup</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('script')
    <script>
        document.addEventListener('livewire:init', function() {

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
