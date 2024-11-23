<?php

use Livewire\Volt\Component;
use App\Models\SubSkpd;
use App\Models\Skpd;
use function Livewire\Volt\{computed, state};
use Livewire\WithPagination;
use App\Models\Program;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $paginate = 10;
    public $search = '';

    public $nama, $kode, $skpd_id, $subSkpd_id;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'subSkpd' => SubSkpd::with('skpd')
                        ->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('kode', 'like', '%' . $this->search . '%')
                        ->paginate($this->paginate),
            'skpd' => Skpd::orderBy('kode', 'asc')->get()
        ];
    }

    public function store()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required',
            'skpd_id' => 'required'
        ], [
            'nama.required' => 'Nama tidak boleh kosong',
            'kode.required' => 'Kode tidak boleh kosong',
            'skpd_id.required' => 'SKPD harus dipilih'
        ]);

        SubSkpd::create([
            'nama' => $this->nama,
            'kode' => $this->kode,
            'skpd_id' => $this->skpd_id
        ]);

        $this->reset(['nama', 'kode', 'skpd_id']);
        $this->dispatch('tambahAlertToast');
    }

    public function edit($id)
    {
        $subSkpd = SubSkpd::find($id);
        $this->subSkpd_id = $subSkpd->id;
        $this->nama = $subSkpd->nama;
        $this->kode = $subSkpd->kode;
        $this->skpd_id = $subSkpd->skpd_id;
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required|unique:sub_skpds,kode,' . $this->subSkpd_id,
            'skpd_id' => 'required'
        ]);

        try {
            $subSkpd = SubSkpd::find($this->subSkpd_id);
            $subSkpd->update([
                'nama' => $this->nama,
                'kode' => $this->kode,
                'skpd_id' => $this->skpd_id
            ]);

            $this->reset(['nama', 'kode', 'skpd_id', 'subSkpd_id']);
            $this->dispatch('updateAlertToast');
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast');
        }
    }

    public function close()
    {
        $this->reset(['nama', 'kode', 'skpd_id', 'subSkpd_id']);
    }

    public function confirmDelete($id)
    {
        $this->subSkpd_id = $id;
        $this->dispatch('confirmDelete', $id);
    }

    public function delete($id)
    {
        $subSkpd = SubSkpd::find($id)->first();
        if ($subSkpd) {
            // Check if related data exists
            if (Program::where('sub_skpd_id', $id)->exists()) {
            $this->dispatch('errorAlertToast', ['type' => 'error', 'message' => 'Data cannot be deleted because it is related to other data']);
            return;
            } else {
            $subSkpd->delete();
            $this->dispatch('deleteAlertToast', ['type' => 'success', 'message' => 'Data successfully deleted']);
            }
        } else {
            $this->dispatch('errorAlertToast', ['type' => 'error', 'message' => 'Data not found']);
        }
    }
}; ?>

<div>
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Sub SKPD</div>
                    <div class="card-tools">
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="fa fa-plus"></i> Tambah Sub SKPD
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
                                <th>Kode/SKPD</th>
                                <th>Kode/Nama Sub SKPD</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subSkpd as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>[{{ $item->skpd->urusanPelaksana->kode }}.{{ $item->skpd->kode }}] {{ $item->skpd->nama }}</td>
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
                <div class="justify-content-between">
                    {{ $subSkpd->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" wire:ignore.self aria-labelledby="modalTambahLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Sub SKPD</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit="store">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">SKPD</label>
                            <select class="form-control" wire:model="skpd_id">
                                <option value="">Pilih SKPD</option>
                                @foreach($skpd as $s)
                                    <option value="{{ $s->id }}">[{{ $s->urusanPelaksana->kode }}.{{ $s->kode }}] {{ $s->nama }}</option>
                                @endforeach
                            </select>
                            @error('skpd_id') <span class="text-danger">{{ $message }}</span> @enderror
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
    <div class="modal fade" id="modalEdit" tabindex="-1" wire:ignore.self aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Sub SKPD</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit="update">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">SKPD</label>
                            <select class="form-control" wire:model="skpd_id">
                                <option value="">Pilih SKPD</option>
                                @foreach($skpd as $s)
                                    <option value="{{ $s->id }}">[{{ $s->urusanPelaksana->kode }}{{ $s->kode }}] {{ $s->nama }}</option>
                                @endforeach
                            </select>
                            @error('skpd_id') <span class="text-danger">{{ $message }}</span> @enderror
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
