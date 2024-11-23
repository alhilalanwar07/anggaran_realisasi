<?php

use Livewire\Volt\Component;
use App\Models\JenisAkun;
use App\Models\KelompokAkun;
use function Livewire\Volt\{computed, state};
use Livewire\WithPagination;
use App\Models\ObyekAkun;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $paginate = 10;
    public $search = '';

    public $nama, $kode, $kelompok_akun_id, $jenis_akun_id;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'jenisAkun' => JenisAkun::with('kelompokAkun')
                        ->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('kode', 'like', '%' . $this->search . '%')
                        ->paginate($this->paginate),
            'kelompokAkun' => KelompokAkun::orderBy('kode', 'asc')->get()
        ];
    }

    public function store()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required|unique:jenis_akuns,kode',
            'kelompok_akun_id' => 'required'
        ], [
            'nama.required' => 'Nama tidak boleh kosong',
            'kode.required' => 'Kode tidak boleh kosong',
            'kode.unique' => 'Kode sudah digunakan',
            'kelompok_akun_id.required' => 'Kelompok Akun harus dipilih'
        ]);

        JenisAkun::create([
            'nama' => $this->nama,
            'kode' => $this->kode,
            'kelompok_akun_id' => $this->kelompok_akun_id
        ]);

        $this->reset(['nama', 'kode', 'kelompok_akun_id']);
        $this->dispatch('tambahAlertToast');
    }

    public function edit($id)
    {
        $jenisAkun = JenisAkun::find($id);
        $this->jenis_akun_id = $jenisAkun->id;
        $this->nama = $jenisAkun->nama;
        $this->kode = $jenisAkun->kode;
        $this->kelompok_akun_id = $jenisAkun->kelompok_akun_id;
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required|unique:jenis_akuns,kode,' . $this->jenis_akun_id,
            'kelompok_akun_id' => 'required'
        ]);

        try {
            $jenisAkun = JenisAkun::find($this->jenis_akun_id);
            $jenisAkun->update([
                'nama' => $this->nama,
                'kode' => $this->kode,
                'kelompok_akun_id' => $this->kelompok_akun_id
            ]);

            $this->reset(['nama', 'kode', 'kelompok_akun_id', 'jenis_akun_id']);
            $this->dispatch('updateAlertToast');
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast');
        }
    }

    public function close()
    {
        $this->reset(['nama', 'kode', 'kelompok_akun_id', 'jenis_akun_id']);
    }

    public function confirmDelete($id)
    {
        $this->jenis_akun_id = $id;
        $this->dispatch('confirmDelete', $id);
    }

    public function delete($id)
    {
        try {
            $jenisAkun = JenisAkun::find($id)->first();
            if ($jenisAkun) {
            if (ObyekAkun::where('jenis_akun_id', $id)->exists()) {
                $this->dispatch('errorAlertToast', 'Jenis Akun tidak bisa dihapus karena sudah digunakan');
                return;
            }
            $jenisAkun->delete();
            $this->dispatch('deleteAlertToast');
            }
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast', 'Terjadi kesalahan saat menghapus Jenis Akun');
        }
    }
}; ?>

<div>
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Jenis Akun</div>
                    <div class="card-tools">
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="fa fa-plus"></i> Tambah Jenis Akun
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
                                <th>Kelompok Akun</th>
                                <th>Kode/Nama Jenis Akun</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jenisAkun as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>[{{ $item->kelompokAkun->akun->kode }}.{{ $item->kelompokAkun->kode }}] {{ $item->kelompokAkun->nama }}</td>
                                <td>[{{ $item->kode }}] {{ $item->nama }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" wire:click="edit({{ $item->id }})" data-bs-toggle="modal" data-bs-target="#modalEdit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" wire:click="confirmDelete({{ $item->id }})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="justify-content-between">
                    {{ $jenisAkun->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" wire:ignore.self aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jenis Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kelompok Akun</label>
                            <select class="form-control" wire:model="kelompok_akun_id">
                                <option value="">Pilih Kelompok Akun</option>
                                @foreach($kelompokAkun as $ka)
                                    <option value="{{ $ka->id }}">[{{ $ka->kode }}] {{ $ka->nama }}</option>
                                @endforeach
                            </select>
                            @error('kelompok_akun_id') <span class="text-danger">{{ $message }}</span> @enderror
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
                    <h5 class="modal-title">Edit Jenis Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kelompok Akun</label>
                            <select class="form-control" wire:model="kelompok_akun_id">
                                <option value="">Pilih Kelompok Akun</option>
                                @foreach($kelompokAkun as $ka)
                                    <option value="{{ $ka->id }}">[{{ $ka->kode }}] {{ $ka->nama }}</option>
                                @endforeach
                            </select>
                            @error('kelompok_akun_id') <span class="text-danger">{{ $message }}</span> @enderror
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
