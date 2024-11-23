<?php

use Livewire\Volt\Component;
use App\Models\Skpd;
use App\Models\UrusanPelaksana;
use App\Models\SubSkpd;
use function Livewire\Volt\{computed, state};
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $paginate = 10;
    public $search = '';

    // Form fields
    public $nama, $kode, $urusan_pelaksana_id, $skpd_id;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'skpd' => Skpd::with('urusanPelaksana')
                        ->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('kode', 'like', '%' . $this->search . '%')
                        ->paginate($this->paginate),
            'urusanPelaksana' => UrusanPelaksana::orderBy('kode', 'asc')->get()
        ];
    }

    public function store()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required',
            'urusan_pelaksana_id' => 'required'
        ], [
            'nama.required' => 'Nama tidak boleh kosong',
            'kode.required' => 'Kode tidak boleh kosong',
            'urusan_pelaksana_id.required' => 'Urusan Pelaksana harus dipilih'
        ]);

        Skpd::create([
            'nama' => $this->nama,
            'kode' => $this->kode,
            'urusan_pelaksana_id' => $this->urusan_pelaksana_id
        ]);

        $this->reset(['nama', 'kode', 'urusan_pelaksana_id']);
        $this->dispatch('tambahAlertToast');
    }

    public function edit($id)
    {
        $skpd = Skpd::find($id);
        $this->skpd_id = $skpd->id;
        $this->nama = $skpd->nama;
        $this->kode = $skpd->kode;
        $this->urusan_pelaksana_id = $skpd->urusan_pelaksana_id;
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required',
            'urusan_pelaksana_id' => 'required'
        ]);

        try {
            $skpd = Skpd::find($this->skpd_id);
            $skpd->update([
                'nama' => $this->nama,
                'kode' => $this->kode,
                'urusan_pelaksana_id' => $this->urusan_pelaksana_id
            ]);

            $this->reset(['nama', 'kode', 'urusan_pelaksana_id', 'skpd_id']);
            $this->dispatch('updateAlertToast');
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast');
        }
    }

    public function close()
    {
        $this->reset(['nama', 'kode', 'urusan_pelaksana_id', 'skpd_id']);
    }

    public function confirmDelete($id)
    {
        $this->skpd_id = $id;
        $this->dispatch('confirmDelete', $id);
    }

    public function delete($id)
    {
        $skpd = Skpd::find($id)->first();
        if ($skpd) {
            if(SubSkpd::where('skpd_id', $id)->exists()) {
            $this->dispatch('errorAlertToast', ['type' => 'error', 'message' => 'Data tidak dapat dihapus karena masih terkait dengan Sub SKPD']);
            return;
            }
            $skpd->delete();
            $this->dispatch('deleteAlertToast', ['type' => 'success', 'message' => 'Data berhasil dihapus']);
        } else {
            $this->dispatch('errorAlertToast', ['type' => 'error', 'message' => 'Data tidak ditemukan']);
        }
    }
}; ?>

<div>
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">SKPD</div>
                    <div class="card-tools">
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="fa fa-plus"></i> Tambah SKPD
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
                                <th>Kode/Urusan Pelaksana</th>
                                <th>Kode/Nama SKPD</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($skpd as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>[{{ $item->urusanPelaksana->kode }}] {{ $item->urusanPelaksana->nama }}</td>
                                <td>[{{ $item->kode }}] {{ $item->nama }}</td>
                                <td></td>
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
                    {{ $skpd->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" wire:ignore.self  aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah SKPD</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit="store">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Urusan Pelaksana</label>
                            <select class="form-control" wire:model="urusan_pelaksana_id">
                                <option value="">Pilih Urusan Pelaksana</option>
                                @foreach($urusanPelaksana as $up)
                                    <option value="{{ $up->id }}">
                                        [{{ $up->kode }}] {{ $up->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('urusan_pelaksana_id') <span class="text-danger">{{ $message }}</span> @enderror
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
    <div class="modal fade" id="modalEdit" tabindex="-1" wire:ignore.self  aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit SKPD</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit="update">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Urusan Pelaksana</label>
                            <select class="form-control" wire:model="urusan_pelaksana_id">
                                <option value="">Pilih Urusan Pelaksana</option>
                                @foreach($urusanPelaksana as $up)
                                    <option value="{{ $up->id }}">
                                        [{{ $up->kode }}] {{ $up->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('urusan_pelaksana_id') <span class="text-danger">{{ $message }}</span> @enderror
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
