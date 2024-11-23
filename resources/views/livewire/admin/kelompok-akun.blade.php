<?php

use Livewire\Volt\Component;
use App\Models\KelompokAkun;
use App\Models\Akun;
use function Livewire\Volt\{computed, state};
use Livewire\WithPagination;
use App\Models\JenisAkun;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $paginate = 10;
    public $search = '';

    public $nama, $kode, $akun_id, $kelompok_akun_id;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'kelompokAkun' => KelompokAkun::with('akun')
                        ->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('kode', 'like', '%' . $this->search . '%')
                        ->paginate($this->paginate),
            'akun' => Akun::orderBy('kode', 'asc')->get()
        ];
    }

    public function store()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required',
            'akun_id' => 'required'
        ], [
            'nama.required' => 'Nama tidak boleh kosong',
            'kode.required' => 'Kode tidak boleh kosong',
            'akun_id.required' => 'Akun harus dipilih'
        ]);

        KelompokAkun::create([
            'nama' => $this->nama,
            'kode' => $this->kode,
            'akun_id' => $this->akun_id
        ]);

        $this->reset(['nama', 'kode', 'akun_id']);
        $this->dispatch('tambahAlertToast');
    }

    public function edit($id)
    {
        $kelompokAkun = KelompokAkun::find($id);
        $this->kelompok_akun_id = $kelompokAkun->id;
        $this->nama = $kelompokAkun->nama;
        $this->kode = $kelompokAkun->kode;
        $this->akun_id = $kelompokAkun->akun_id;
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required',
            'akun_id' => 'required'
        ]);

        try {
            $kelompokAkun = KelompokAkun::find($this->kelompok_akun_id);
            $kelompokAkun->update([
                'nama' => $this->nama,
                'kode' => $this->kode,
                'akun_id' => $this->akun_id
            ]);

            $this->reset(['nama', 'kode', 'akun_id', 'kelompok_akun_id']);
            $this->dispatch('updateAlertToast');
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast');
        }
    }

    public function close()
    {
        $this->reset(['nama', 'kode', 'akun_id', 'kelompok_akun_id']);
    }

    public function confirmDelete($id)
    {
        $this->kelompok_akun_id = $id;
        $this->dispatch('confirmDelete', $id);
    }

    public function delete($id)
    {
        try {
            $kelompokAkun = KelompokAkun::findOrFail($id)->first();
            if (JenisAkun::where('kelompok_akun_id', $id)->count() > 0) {
            $this->dispatch('errorAlertToast', 'Kelompok Akun tidak bisa dihapus karena masih digunakan di Jenis Akun');
            } else {
            $kelompokAkun->delete();
            $this->dispatch('deleteAlertToast');
            }
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast', 'Terjadi kesalahan saat menghapus Kelompok Akun');
        }
    }
}; ?>

<div>
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Kelompok Akun</div>
                    <div class="card-tools">
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="fa fa-plus"></i> Tambah Kelompok Akun
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
                                <th>Akun</th>
                                <th>Kode/Nama Kelompok Akun</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelompokAkun as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>[{{ $item->akun->kode }}] {{ $item->akun->nama }}</td>
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
                    {{ $kelompokAkun->links() }}

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" wire:ignore.self aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kelompok Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Akun</label>
                            <select class="form-control" wire:model="akun_id">
                                <option value="">Pilih Akun</option>
                                @foreach($akun as $a)
                                <option value="{{ $a->id }}">[{{ $a->kode }}] {{ $a->nama }}</option>
                                @endforeach
                            </select>
                            @error('akun_id') <span class="text-danger">{{ $message }}</span> @enderror
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
    <div class="modal fade" id="modalEdit" tabindex="-1" wire:ignore.self aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kelompok Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Akun</label>
                            <select class="form-control" wire:model="akun_id">
                                <option value="">Pilih Akun</option>
                                @foreach($akun as $a)
                                <option value="{{ $a->id }}">[{{ $a->kode }}] {{ $a->nama }}</option>
                                @endforeach
                            </select>
                            @error('akun_id') <span class="text-danger">{{ $message }}</span> @enderror
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
