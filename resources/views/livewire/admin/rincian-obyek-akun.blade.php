<?php

use Livewire\Volt\Component;
use App\Models\RincianObyekAkun;
use App\Models\ObyekAkun;
use function Livewire\Volt\{computed, state};
use Livewire\WithPagination;
use App\Models\SubRincianObyekAkun;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $paginate = 10;
    public $search = '';

    public $nama, $kode, $obyek_akun_id, $rincian_obyek_akun_id;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'rincianObyekAkun' => RincianObyekAkun::with('obyekAkun.jenisAkun.kelompokAkun.akun')
                        ->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('kode', 'like', '%' . $this->search . '%')
                        ->paginate($this->paginate),
            'obyekAkun' => ObyekAkun::orderBy('kode', 'asc')->get()
        ];
    }

    public function store()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required',
            'obyek_akun_id' => 'required'
        ], [
            'nama.required' => 'Nama tidak boleh kosong',
            'kode.required' => 'Kode tidak boleh kosong',
            'obyek_akun_id.required' => 'Obyek Akun harus dipilih'
        ]);

        RincianObyekAkun::create([
            'nama' => $this->nama,
            'kode' => $this->kode,
            'obyek_akun_id' => $this->obyek_akun_id
        ]);

        $this->reset(['nama', 'kode', 'obyek_akun_id']);
        $this->dispatch('tambahAlertToast');
    }

    public function edit($id)
    {
        $rincianObyekAkun = RincianObyekAkun::find($id);
        $this->rincian_obyek_akun_id = $rincianObyekAkun->id;
        $this->nama = $rincianObyekAkun->nama;
        $this->kode = $rincianObyekAkun->kode;
        $this->obyek_akun_id = $rincianObyekAkun->obyek_akun_id;
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required',
            'obyek_akun_id' => 'required'
        ]);

        try {
            $rincianObyekAkun = RincianObyekAkun::find($this->rincian_obyek_akun_id);
            $rincianObyekAkun->update([
                'nama' => $this->nama,
                'kode' => $this->kode,
                'obyek_akun_id' => $this->obyek_akun_id
            ]);

            $this->reset(['nama', 'kode', 'obyek_akun_id', 'rincian_obyek_akun_id']);
            $this->dispatch('updateAlertToast');
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast');
        }
    }

    public function close()
    {
        $this->reset(['nama', 'kode', 'obyek_akun_id', 'rincian_obyek_akun_id']);
    }

    public function confirmDelete($id)
    {
        $this->rincian_obyek_akun_id = $id;
        $this->dispatch('confirmDelete', $id);
    }

    public function delete($id)
    {
        try {
            $rincianObyekAkun = RincianObyekAkun::find($id)->first();
            if ($rincianObyekAkun) {
            if (SubRincianObyekAkun::where('rincian_obyek_akun_id', $id)->count() > 0) {
                $this->dispatch('errorAlertToast', 'Rincian Obyek Akun tidak bisa dihapus karena memiliki data Sub Rincian Obyek Akun');
            } else {
                $rincianObyekAkun->delete();
                $this->dispatch('deleteAlertToast');
            }
            }
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast', 'Terjadi kesalahan saat menghapus data');
        }
    }
}; ?>

<div>
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Rincian Obyek Akun</div>
                    <div class="card-tools">
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="fa fa-plus"></i> Tambah Rincian Obyek Akun
                        </button>
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
                        <input wire:model.live="search" type="text" class="form-control w-auto" placeholder="Cari Rincian Obyek Akun...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Obyek Akun</th>
                                <th>Kode/Nama Rincian Obyek Akun</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rincianObyekAkun as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>[{{ $item->obyekAkun->jenisAkun->kelompokAkun->akun->kode }}.{{ $item->obyekAkun->jenisAkun->kelompokAkun->kode }}.{{ $item->obyekAkun->jenisAkun->kode }}.{{ $item->obyekAkun->kode }}] {{ $item->obyekAkun->nama }}</td>
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
                    {{ $rincianObyekAkun->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" wire:ignore.self aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Rincian Obyek Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Obyek Akun</label>
                            <select class="form-control" wire:model="obyek_akun_id">
                                <option value="">Pilih Obyek Akun</option>
                                @foreach($obyekAkun as $oa)
                                    <option value="{{ $oa->id }}">[{{ $oa->kode }}] {{ $oa->nama }}</option>
                                @endforeach
                            </select>
                            @error('obyek_akun_id') <span class="text-danger">{{ $message }}</span> @enderror
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
                    <h5 class="modal-title">Edit Rincian Obyek Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Obyek Akun</label>
                            <select class="form-control" wire:model="obyek_akun_id">
                                <option value="">Pilih Obyek Akun</option>
                                @foreach($obyekAkun as $oa)
                                    <option value="{{ $oa->id }}">[{{ $oa->kode }}] {{ $oa->nama }}</option>
                                @endforeach
                            </select>
                            @error('obyek_akun_id') <span class="text-danger">{{ $message }}</span> @enderror
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
