<?php

use Livewire\Volt\Component;
use App\Models\ObyekAkun;
use App\Models\JenisAkun;
use function Livewire\Volt\{computed, state};
use Livewire\WithPagination;
use App\Models\RincianObyekAkun;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $paginate = 10;
    public $search = '';

    public $nama, $kode, $jenis_akun_id, $obyek_akun_id;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'obyekAkun' => ObyekAkun::with('jenisAkun')
                        ->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('kode', 'like', '%' . $this->search . '%')
                        ->paginate($this->paginate),
            'jenisAkun' => JenisAkun::orderBy('kode', 'asc')->get()
        ];
    }

    public function store()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required',
            'jenis_akun_id' => 'required'
        ], [
            'nama.required' => 'Nama tidak boleh kosong',
            'kode.required' => 'Kode tidak boleh kosong',
            'jenis_akun_id.required' => 'Jenis Akun harus dipilih'
        ]);

        ObyekAkun::create([
            'nama' => $this->nama,
            'kode' => $this->kode,
            'jenis_akun_id' => $this->jenis_akun_id
        ]);

        $this->reset(['nama', 'kode', 'jenis_akun_id']);
        $this->dispatch('tambahAlertToast');
    }

    public function edit($id)
    {
        $obyekAkun = ObyekAkun::find($id);
        $this->obyek_akun_id = $obyekAkun->id;
        $this->nama = $obyekAkun->nama;
        $this->kode = $obyekAkun->kode;
        $this->jenis_akun_id = $obyekAkun->jenis_akun_id;
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required',
            'jenis_akun_id' => 'required'
        ]);

        try {
            $obyekAkun = ObyekAkun::find($this->obyek_akun_id);
            $obyekAkun->update([
                'nama' => $this->nama,
                'kode' => $this->kode,
                'jenis_akun_id' => $this->jenis_akun_id
            ]);

            $this->reset(['nama', 'kode', 'jenis_akun_id', 'obyek_akun_id']);
            $this->dispatch('updateAlertToast');
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast');
        }
    }

    public function close()
    {
        $this->reset(['nama', 'kode', 'jenis_akun_id', 'obyek_akun_id']);
    }

    public function confirmDelete($id)
    {
        $this->obyek_akun_id = $id;
        $this->dispatch('confirmDelete', $id);
    }

    public function delete($id)
    {
        try {
            $obyekAkun = ObyekAkun::find($id)->first();
            if ($obyekAkun) {
            if (RincianObyekAkun::where('obyek_akun_id', $id)->count() > 0) {
                $this->dispatch('errorAlertToast', 'Obyek Akun tidak bisa dihapus karena memiliki relasi dengan Rincian Obyek Akun');
                return;
            }
            $obyekAkun->delete();
            $this->dispatch('deleteAlertToast');
            }
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast', 'Terjadi kesalahan saat menghapus Obyek Akun');
        }
    }
}; ?>

<div>
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Obyek Akun</div>
                    <div class="card-tools">
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="fa fa-plus"></i> Tambah Obyek Akun
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
                                <th>Jenis Akun</th>
                                <th>Kode/Nama Obyek Akun</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($obyekAkun as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>[{{ $item->jenisAkun->kelompokAkun->akun->kode }}.{{ $item->jenisAkun->kelompokAkun->kode }}.{{ $item->jenisAkun->kode }}] {{ $item->jenisAkun->nama }}</td>
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
                    {{ $obyekAkun->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" wire:ignore.self aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Obyek Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Jenis Akun</label>
                            <select class="form-control" wire:model="jenis_akun_id">
                                <option value="">Pilih Jenis Akun</option>
                                @foreach($jenisAkun as $ja)
                                    <option value="{{ $ja->id }}">[{{ $ja->kode }}] {{ $ja->nama }}</option>
                                @endforeach
                            </select>
                            @error('jenis_akun_id') <span class="text-danger">{{ $message }}</span> @enderror
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
                    <h5 class="modal-title">Edit Obyek Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Jenis Akun</label>
                            <select class="form-control" wire:model="jenis_akun_id">
                                <option value="">Pilih Jenis Akun</option>
                                @foreach($jenisAkun as $ja)
                                    <option value="{{ $ja->id }}">[{{ $ja->kode }}] {{ $ja->nama }}</option>
                                @endforeach
                            </select>
                            @error('jenis_akun_id') <span class="text-danger">{{ $message }}</span> @enderror
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
