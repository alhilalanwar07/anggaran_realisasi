<?php

use Livewire\Volt\Component;
use App\Models\Kegiatan;
use App\Models\Program;
use function Livewire\Volt\{computed, state};
use Livewire\WithPagination;
use App\Models\SubKegiatan;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $paginate = 10;
    public $search = '';

    public $nama, $kode, $program_id, $kegiatan_id;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'kegiatan' => Kegiatan::with('program.subSkpd.skpd.urusanPelaksana')
                        ->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('kode', 'like', '%' . $this->search . '%')
                        ->paginate($this->paginate),
            'program' => Program::with('subSkpd.skpd.urusanPelaksana')
                        ->orderBy('kode', 'asc')
                        ->get()
        ];
    }

    public function store()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required',
            'program_id' => 'required'
        ], [
            'nama.required' => 'Nama tidak boleh kosong',
            'kode.required' => 'Kode tidak boleh kosong',
            'program_id.required' => 'Program harus dipilih'
        ]);

        Kegiatan::create([
            'nama' => $this->nama,
            'kode' => $this->kode,
            'program_id' => $this->program_id
        ]);

        $this->reset(['nama', 'kode', 'program_id']);
        $this->dispatch('tambahAlertToast');
    }

    public function edit($id)
    {
        $kegiatan = Kegiatan::find($id);
        $this->kegiatan_id = $kegiatan->id;
        $this->nama = $kegiatan->nama;
        $this->kode = $kegiatan->kode;
        $this->program_id = $kegiatan->program_id;
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required',
            'program_id' => 'required'
        ]);

        try {
            $kegiatan = Kegiatan::find($this->kegiatan_id);
            $kegiatan->update([
                'nama' => $this->nama,
                'kode' => $this->kode,
                'program_id' => $this->program_id
            ]);

            $this->reset(['nama', 'kode', 'program_id', 'kegiatan_id']);
            $this->dispatch('updateAlertToast');
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast');
        }
    }

    public function close()
    {
        $this->reset(['nama', 'kode', 'program_id', 'kegiatan_id']);
    }

    public function confirmDelete($id)
    {
        $this->kegiatan_id = $id;
        $this->dispatch('confirmDelete', $id);
    }

    public function delete($id)
    {
        try {
            $kegiatan = Kegiatan::find($id)->first();
            if ($kegiatan) {
            if (SubKegiatan::where('kegiatan_id', $id)->count() > 0) {
                $this->dispatch('errorAlertToast', 'Kegiatan tidak dapat dihapus karena memiliki sub kegiatan');
            } else {
                $kegiatan->delete();
                $this->dispatch('deleteAlertToast');
            }
            }
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast', 'Terjadi kesalahan saat menghapus kegiatan');
        }
    }
}; ?>

<div>
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Kegiatan</div>
                    <div class="card-tools">
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="fa fa-plus"></i> Tambah Kegiatan
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
                                <th>Program</th>
                                <th>Kode/Nama Kegiatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kegiatan as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>[{{ $item->program->subSkpd->skpd->urusanPelaksana->kode }}.{{ $item->program->subSkpd->skpd->kode }}.{{ $item->program->subSkpd->kode }}.{{ $item->program->kode }}] {{ $item->program->nama }}</td>
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
                    {{ $kegiatan->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" wire:ignore.self data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Program</label>
                            <select class="form-control" wire:model="program_id">
                                <option value="">Pilih Program</option>
                                @foreach($program as $p)
                                <option value="{{ $p->id }}">[{{ $p->kode }}] {{ $p->nama }}</option>
                                @endforeach
                            </select>
                            @error('program_id') <span class="text-danger">{{ $message }}</span> @enderror
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
    <div class="modal fade" id="modalEdit" tabindex="-1" wire:ignore.self data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Program</label>
                            <select class="form-control" wire:model="program_id">
                                <option value="">Pilih Program</option>
                                @foreach($program as $p)
                                <option value="{{ $p->id }}">[{{ $p->kode }}] {{ $p->nama }}</option>
                                @endforeach
                            </select>
                            @error('program_id') <span class="text-danger">{{ $message }}</span> @enderror
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
