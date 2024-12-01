<?php

use Livewire\Volt\Component;
use App\Models\UrusanPelaksana;
use App\Models\Skpd;
use Livewire\WithPagination;
use App\Models\Urusan;

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
            'urusanPelaksana' => UrusanPelaksana::with('urusan')
                                ->where('nama', 'like', '%' . $this->search . '%')
                                ->orWhere('kode', 'like', '%' . $this->search . '%')
                                ->paginate($this->paginate),
            'urusan' => Urusan::orderBy('kode', 'asc')->get()
        ];
    }

    public $nama, $kode, $urusanPelaksana_id, $urusan_id, $skpd_id;

    public function store()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required',
            'urusan_id' => 'required',
        ], [
            'nama.required' => 'Nama tidak boleh kosong',
            'kode.required' => 'Kode tidak boleh kosong',
            'urusan_id.required' => 'Urusan tidak boleh kosong'
        ]);

        UrusanPelaksana::create([
            'nama' => $this->nama,
            'kode' => $this->kode,
            'urusan_id' => $this->urusan_id
        ]);

        $this->reset('nama', 'kode');

        $this->dispatch('tambahAlertToast', [
            ['title' => 'Success', 'text' => 'Data berhasil ditambahkan', 'type' => 'success', 'timeout' => 3000]
        ]);
    }

    public function edit($id)
    {
        $urusanPelaksana = UrusanPelaksana::with('urusan')->find($id);

        $this->urusanPelaksana_id = $urusanPelaksana->id;
        $this->nama = $urusanPelaksana->nama;
        $this->kode = $urusanPelaksana->kode;
        $this->urusan_id = $urusanPelaksana->urusan_id;
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required',
            'urusan_id' => 'required',
        ], [
            'nama.required' => 'Nama tidak boleh kosong',
            'kode.required' => 'Kode tidak boleh kosong',
            'urusan_id.required' => 'Urusan tidak boleh kosong'
        ]);

        try {
            $urusanPelaksana = UrusanPelaksana::find($this->urusanPelaksana_id);

            $urusanPelaksana->update([
                'nama' => $this->nama,
                'kode' => $this->kode,
                'urusan_id' => $this->urusan_id
            ]);

            $this->reset('nama', 'kode', 'urusanPelaksana_id', 'urusan_id');

            $this->dispatch('updateAlertToast', [
                ['title' => 'Success', 'text' => 'Data berhasil diperbarui', 'type' => 'success', 'timeout' => 3000]
            ]);
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast', ['type' => 'error', 'message' => 'Terjadi kesalahan saat memperbarui data']);
        }
    }

    public function close()
    {
        $this->reset('nama', 'kode', 'urusanPelaksana_id');
    }

    public function confirmDelete($id)
    {
        $this->urusanPelaksana_id = $id;
        $this->dispatch('confirmDelete', $id);
    }

    public function delete($id)
    {
        $urusanPelaksana = UrusanPelaksana::find($id)->first();
        if ($urusanPelaksana) {
            if(Skpd::where('urusan_pelaksana_id', $id)->exists()) {
                $this->dispatch('errorAlertToast', ['type' => 'error', 'message' => 'Data cannot be deleted because it is related to other data']);
                return;
            } else {
                $urusanPelaksana->delete();
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
                <div class="card-head-row mb-3">
                    <div class="card-title">Urusan Pelaksana</div>
                    <div class="card-tools">
                        <a href="#" class="btn btn-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalTambah" wire:click="close">
                            <span class="btn-label">
                                <i class="fa fa-plus"></i>
                            </span>
                            Tambah Urusan Pelaksana
                        </a>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <div class="card-head-row">
                    <div class="d-flex mb-3 justify-content-between gap-2">
                        <select wire:model.live="paginate" class="form-select w-auto">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                        </select>
                        <input wire:model.live="search" type="text" class="form-control w-auto" placeholder="Cari Urusan Pelaksana...">
                    </div>
                </div>
                <div class="table table-responsive">
                    <table class="table table-hover table-borderless">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Kode/Urusan</th>
                                <th>Kode/Urusan Pelaksana</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @forelse($urusanPelaksana as $item)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>
                                    [{{ $item->urusan->kode }}] -
                                    {{ $item->urusan->nama }}</td>
                                <td>[{{ $item->kode }}] - {{ $item->nama }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm mb-1" wire:click="edit({{ $item->id }})" data-bs-toggle="modal" data-bs-target="#modalEdit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm mb-1   " wire:click="confirmDelete({{ $item->id }})">
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
                    {{ $urusanPelaksana->links() }}
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore class="modal fade" id="modalTambah" aria-labelledby="modalTambahLabel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Urusan Pelaksana</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="store">
                        @csrf
                        <div class="mb-3">
                            <label for="urusan_id" class="form-label">Urusan</label>
                            <select wire:model="urusan_id" class="form-select" id="urusan_id">
                                <option value="">Pilih Urusan</option>
                                @foreach($urusan as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                @endforeach
                            </select>
                            @error('urusan_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
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

    <div wire:ignore class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">Edit Urusan Pelaksana</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="update">
                        @csrf
                        <div class="mb-3">
                            <label for="urusan_id" class="form-label">Urusan</label>
                            <select wire:model="urusan_id" class="form-select" id="urusan_id">
                                <option value="">Pilih Urusan</option>
                                @foreach($urusan as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                @endforeach
                            </select>
                            @error('urusan_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
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
        </div>
    </div>

    @push('scripts')
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
