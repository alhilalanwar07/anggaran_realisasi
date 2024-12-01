<?php

use Livewire\Volt\Component;
use App\Models\SubRincianObyekAkun;
use App\Models\RincianObyekAkun;
use function Livewire\Volt\{computed, state};
use Livewire\WithPagination;
use App\Models\Anggaran;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $paginate = 10;
    public $search = '';

    public $nama, $kode, $rincian_obyek_akun_id, $sub_rincian_obyek_akun_id;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'subRincianObyekAkun' => SubRincianObyekAkun::with('rincianObyekAkun.obyekAkun.jenisAkun.kelompokAkun.akun')
                        ->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('kode', 'like', '%' . $this->search . '%')
                        ->paginate($this->paginate),
            'rincianObyekAkun' => RincianObyekAkun::orderBy('kode', 'asc')->get()
        ];
    }

    public function store()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required',
            'rincian_obyek_akun_id' => 'required'
        ], [
            'nama.required' => 'Nama tidak boleh kosong',
            'kode.required' => 'Kode tidak boleh kosong',
            'rincian_obyek_akun_id.required' => 'Rincian Obyek Akun harus dipilih'
        ]);

        SubRincianObyekAkun::create([
            'nama' => $this->nama,
            'kode' => $this->kode,
            'rincian_obyek_akun_id' => $this->rincian_obyek_akun_id
        ]);

        $this->reset(['nama', 'kode', 'rincian_obyek_akun_id']);
        $this->dispatch('tambahAlertToast');
    }

    public function edit($id)
    {
        $subRincianObyekAkun = SubRincianObyekAkun::find($id);
        $this->sub_rincian_obyek_akun_id = $subRincianObyekAkun->id;
        $this->nama = $subRincianObyekAkun->nama;
        $this->kode = $subRincianObyekAkun->kode;
        $this->rincian_obyek_akun_id = $subRincianObyekAkun->rincian_obyek_akun_id;
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => 'required',
            'rincian_obyek_akun_id' => 'required'
        ]);

        try {
            $subRincianObyekAkun = SubRincianObyekAkun::find($this->sub_rincian_obyek_akun_id);
            $subRincianObyekAkun->update([
                'nama' => $this->nama,
                'kode' => $this->kode,
                'rincian_obyek_akun_id' => $this->rincian_obyek_akun_id
            ]);

            $this->reset(['nama', 'kode', 'rincian_obyek_akun_id', 'sub_rincian_obyek_akun_id']);
            $this->dispatch('updateAlertToast');
        } catch (\Exception $e) {
            $this->dispatch('errorAlertToast');
        }
    }

    public function close()
    {
        $this->reset(['nama', 'kode', 'rincian_obyek_akun_id', 'sub_rincian_obyek_akun_id']);
    }

    public function confirmDelete($id)
    {
        $this->sub_rincian_obyek_akun_id = $id;
        $this->dispatch('confirmDelete', $id);
    }

    public function delete($id)
    {
        try {
            $subRincianObyekAkun = SubRincianObyekAkun::find($id)->first();
            if ($subRincianObyekAkun) {
                if (Anggaran::where('sub_rincian_obyek_akun_id', $id)->count() > 0) {
                    $this->dispatch('errorAlertToast', 'Sub Rincian Obyek Akun tidak bisa dihapus karena memiliki data Anggaran');
                } else {
                    $subRincianObyekAkun->delete();
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
                    <div class="card-title">Sub Rincian Obyek Akun</div>
                    <div class="card-tools">
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="fa fa-plus"></i> Tambah Sub Rincian Obyek Akun
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
                        <input wire:model.live="search" type="text" class="form-control w-auto" placeholder="Cari Sub Rincian Obyek Akun...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Rincian Obyek Akun</th>
                                <th>Kode/Nama Sub Rincian Obyek Akun</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subRincianObyekAkun as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>[{{ $item->rincianObyekAkun->obyekAkun->jenisAkun->kelompokAkun->akun->kode }}.{{ $item->rincianObyekAkun->obyekAkun->jenisAkun->kelompokAkun->kode }}.{{ $item->rincianObyekAkun->obyekAkun->jenisAkun->kode }}.{{ $item->rincianObyekAkun->obyekAkun->kode }}.{{ $item->rincianObyekAkun->kode }}] {{ $item->rincianObyekAkun->nama }}</td>
                                <td>[{{ $item->kode }}] {{ $item->nama }}</td>
                                <td class="gap-2">
                                    <button class="btn btn-primary btn-sm mb-2" wire:click="edit({{ $item->id }})" data-bs-toggle="modal" data-bs-target="#modalEdit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm mb-2" wire:click="confirmDelete({{ $item->id }})">
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
                        {{ $subRincianObyekAkun->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" wire:ignore.self aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Sub Rincian Obyek Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Rincian Obyek Akun</label>
                            <select class="form-control" wire:model="rincian_obyek_akun_id">
                                <option value="">Pilih Rincian Obyek Akun</option>
                                @foreach($rincianObyekAkun as $roa)
                                    <option value="{{ $roa->id }}">[{{ $roa->kode }}] {{ $roa->nama }}</option>
                                @endforeach
                            </select>
                            @error('rincian_obyek_akun_id') <span class="text-danger">{{ $message }}</span> @enderror
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
                    <h5 class="modal-title">Edit Sub Rincian Obyek Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="close"></button>
                </div>
                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Rincian Obyek Akun</label>
                            <select class="form-control" wire:model="rincian_obyek_akun_id">
                                <option value="">Pilih Rincian Obyek Akun</option>
                                @foreach($rincianObyekAkun as $roa)
                                    <option value="{{ $roa->id }}">[{{ $roa->kode }}] {{ $roa->nama }}</option>
                                @endforeach
                            </select>
                            @error('rincian_obyek_akun_id') <span class="text-danger">{{ $message }}</span> @enderror
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
