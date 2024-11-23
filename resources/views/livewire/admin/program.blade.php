<?php

use Livewire\Volt\Component;
use App\Models\Program;
use App\Models\SubSkpd;
use function Livewire\Volt\{computed, state};
use Livewire\WithPagination;
use App\Models\Kegiatan;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $paginate = 10;
    public $search = '';

    public $nama, $kode, $sub_skpd_id, $program_id;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'program' => Program::with('subSkpd.skpd.urusanPelaksana')
                        ->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('kode', 'like', '%' . $this->search . '%')
                        ->paginate($this->paginate),
            'subSkpd' => SubSkpd::with('skpd.urusanPelaksana')
                        ->orderBy('kode', 'asc')
                        ->get()
        ];
    }

    public function store()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required',
            'sub_skpd_id' => 'required'
        ], [
            'nama.required' => 'Nama tidak boleh kosong',
            'kode.required' => 'Kode tidak boleh kosong',
            'sub_skpd_id.required' => 'Sub SKPD harus dipilih'
        ]);

        Program::create([
            'nama' => $this->nama,
            'kode' => $this->kode,
            'sub_skpd_id' => $this->sub_skpd_id
        ]);

        $this->reset(['nama', 'kode', 'sub_skpd_id']);
        $this->dispatch('tambahAlertToast');
    }

    public function edit($id)
    {
        $program = Program::find($id);
        $this->program_id = $program->id;
        $this->nama = $program->nama;
        $this->kode = $program->kode;
        $this->sub_skpd_id = $program->sub_skpd_id;
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required',
            'sub_skpd_id' => 'required'
        ]);

        try {
            $program = Program::find($this->program_id);
            $program->update([
                'nama' => $this->nama,
                'kode' => $this->kode,
                'sub_skpd_id' => $this->sub_skpd_id
            ]);

            $this->reset(['nama', 'kode', 'sub_skpd_id', 'program_id']);
            $this->dispatch('updateAlertToast');
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast');
        }
    }

    public function close()
    {
        $this->reset(['nama', 'kode', 'sub_skpd_id', 'program_id']);
    }

    public function confirmDelete($id)
    {
        $this->program_id = $id;
        $this->dispatch('confirmDelete', $id);
    }

    public function delete($id)
    {
        try {
            $program = Program::find($id)->first();
            if ($program) {
                if(Kegiatan::where('program_id', $id)->count() > 0) {
                    $this->dispatch('errorAlertToast', ['type' => 'error', 'message' => 'Data cannot be deleted because it is related to other data']);
                } else {
                    $program->delete();
                    $this->dispatch('deleteAlertToast');
                }
            } else {
                $this->dispatch('errorAlertToast', ['type' => 'error', 'message' => 'Data not found']);
            }
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast', ['type' => 'error', 'message' => 'Failed to delete data']);
        }
    }
}; ?>

<div>
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Program</div>
                    <div class="card-tools">
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="fa fa-plus"></i> Tambah Program
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
                                <th>Sub SKPD</th>
                                <th>Kode/Nama Program</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($program as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>[{{ $item->subSkpd->skpd->urusanPelaksana->kode }}.{{ $item->subSkpd->skpd->kode }}.{{ $item->subSkpd->kode }}] {{ $item->subSkpd->nama }}</td>
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
                    {{ $program->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" wire:ignore.self aria-labelledby="modalTambahLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Program</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit="store">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Sub SKPD</label>
                            <select class="form-control" wire:model="sub_skpd_id">
                                <option value="">Pilih Sub SKPD</option>
                                @foreach($subSkpd as $s)
                                <option value="{{ $s->id }}">[{{ $s->skpd->urusanPelaksana->kode }}.{{ $s->skpd->kode }}.{{ $s->kode }}] {{ $s->nama }}</option>
                                @endforeach
                            </select>
                            @error('sub_skpd_id') <span class="text-danger">{{ $message }}</span> @enderror
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
                    <h5 class="modal-title">Edit Program</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit="update">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Sub SKPD</label>
                            <select class="form-control" wire:model="sub_skpd_id">
                                <option value="">Pilih Sub SKPD</option>
                                @foreach($subSkpd as $s)
                                <option value="{{ $s->id }}">[{{ $s->skpd->urusanPelaksana->kode }}.{{ $s->skpd->kode }}.{{ $s->kode }}] {{ $s->nama }}</option>
                                @endforeach
                            </select>
                            @error('sub_skpd_id') <span class="text-danger">{{ $message }}</span> @enderror
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
