<?php

use Livewire\Volt\Component;
use App\Models\Urusan;
use function Livewire\Volt\{computed, state};
use Livewire\WithPagination;
use App\Models\UrusanPelaksana;

new class extends Component {

    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $paginate = 10;
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function with(): array
    {

        return [
            'urusan' => Urusan::where('nama', 'like', '%' . $this->search . '%')
                                ->orWhere('kode', 'like', '%' . $this->search . '%')
                                ->paginate($this->paginate),
        ];
    }

    public $nama, $kode, $urusan_id;

    public function store()
    {
        $this->validate([
            'nama' => 'required|unique:urusans,nama,' . $this->urusan_id,
            'kode' => 'required|unique:urusans,kode,' . $this->urusan_id
        ], [
            'nama.required' => 'Nama tidak boleh kosong',
            'kode.required' => 'Kode tidak boleh kosong',
            'nama.unique' => 'Nama sudah digunakan',
            'kode.unique' => 'Kode sudah digunakan'
        ]);

        Urusan::create([
            'nama' => $this->nama,
            'kode' => $this->kode
        ]);

        $this->reset('nama', 'kode');

        $this->dispatch('tambahAlertToast', [
            ['title' => 'Success', 'text' => 'Data berhasil ditambahkan', 'type' => 'success', 'timeout' => 3000]
        ]);
    }

    public function edit($id)
    {
        $urusan = Urusan::find($id);

        $this->urusan_id = $urusan->id;
        $this->nama = $urusan->nama;
        $this->kode = $urusan->kode;
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required|unique:urusans,nama,' . $this->urusan_id,
            'kode' => 'required|unique:urusans,kode,' . $this->urusan_id
        ], [
            'nama.required' => 'Nama tidak boleh kosong',
            'kode.required' => 'Kode tidak boleh kosong',
            'nama.unique' => 'Nama sudah digunakan',
            'kode.unique' => 'Kode sudah digunakan'
        ]);

        try {
            $urusan = Urusan::find($this->urusan_id);

            $urusan->update([
            'nama' => $this->nama,
            'kode' => $this->kode
            ]);

            $this->reset('nama', 'kode', 'urusan_id');

            $this->dispatch('updateAlertToast', [
                ['title' => 'Success', 'text' => 'Data berhasil diperbarui', 'type' => 'success', 'timeout' => 3000]
            ]);
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast', ['type' => 'error', 'message' => 'Terjadi kesalahan saat memperbarui data']);
        }
    }

    public function close()
    {
        $this->reset('nama', 'kode', 'urusan_id');
    }

    public function confirmDelete($id)
    {
        $this->urusan_id = $id;
        $this->dispatch('confirmDelete', $id);
    }

    public function delete($id)
    {
        $urusan = Urusan::find($id)->first();
        if ($urusan) {
            if(UrusanPelaksana::where('urusan_id', $id)->exists()) {
                $this->dispatch('errorAlertToast', ['type' => 'error', 'message' => 'Data cannot be deleted because it is related to other data']);
                return;
            }else{
                $urusan->delete();
                $this->dispatch('deleteAlertToast', ['type' => 'success', 'message' => 'Data successfully deleted']);
            }
        } else {
            $this->dispatch('errorAlertToast', ['type' => 'error', 'message' => 'Data not found']);
        }
    }
};
?>

<div>
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row mb-3">
                    <div class="card-title">Urusan</div>
                    <div class="card-tools">
                        <a href="#" class="btn btn-info  btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalTambah" wire:click="close">
                            <span class="btn-label">
                                <i class="fa fa-plus"></i>
                            </span>
                            Tambah Urusan
                        </a>
                    </div>
                </div>

            </div>
            <div class="card-body">
                {{-- search --}}
                <div class="card-head-row">
                    <div class="d-flex mb-3 justify-content-between gap-2">
                        <select wire:model.live="paginate" class="form-select w-auto">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                        </select>
                        <input wire:model.live="search" type="text" class="form-control w-auto" placeholder="Cari Urusan...">
                    </div>
                </div>
                <div class="table table-responsive">
                    <table class="table table-hover table-borderless">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Kode</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @forelse($urusan as $item)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->kode }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm mb-1" wire:click="edit({{ $item->id }})" data-bs-toggle="modal" data-bs-target="#modalEdit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm mb-1" wire:click="confirmDelete({{ $item->id }})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Data tidak ditemukan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="card-footer">
                <div class="justify-content-between">
                    {{ $urusan->links() }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Urusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="store">
                        @csrf
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input wire:model="nama" type="text" class="form-control" id="nama">
                            @error('nama') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode</label>
                            <input wire:model="kode" type="text" class="form-control" id="kode">
                            @error('kode') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">Edit Urusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="update">
                        @csrf
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input wire:model="nama" type="text" class="form-control" id="nama">
                            @error('nama') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode</label>
                            <input wire:model="kode" type="text" class="form-control" id="kode">
                            @error('kode') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" wire:click="close">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>w
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
