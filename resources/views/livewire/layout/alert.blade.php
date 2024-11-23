<?php

use Livewire\Volt\Component;

new class extends Component {
    protected $listeners = [
        'tambahAlertToast',
        'updateAlertToast',
        'deleteAlertToast',
        'errorAlertToast'
    ];

    public function tambahAlertToast()
    {
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data berhasil ditambahkan',
            'timer' => 3000,
            'showConfirmButton' => false,
        ]);
    }

    public function updateAlertToast()
    {
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data berhasil diupdate',
            'timer' => 3000,
            'showConfirmButton' => false,
        ]);
    }

    public function deleteAlertToast()
    {
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data berhasil dihapus',
            'timer' => 3000,
            'showConfirmButton' => false,
        ]);
    }

    public function errorAlertToast($params = null)
    {
        $this->dispatch('swal', [
            'icon' => 'error',
            'title' => 'Gagal!',
            'text' => $params['message'] ?? 'Terjadi kesalahan',
            'timer' => 3000,
            'showConfirmButton' => false,
        ]);
    }
}; ?>

<div>
    @script
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('swal', (params) => {
                Swal.fire(params[0]);
            });

            Livewire.on('confirmDelete', (event) => {
                const id = event[0];
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-danger ms-2',
                        cancelButton: 'btn btn-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.dispatch('delete', id);
                    }
                });
            });
        });
    </script>
    @endscript
</div>
