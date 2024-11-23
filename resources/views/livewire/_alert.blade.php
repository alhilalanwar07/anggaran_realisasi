<div>
    @push('script')
    <script>
        document.addEventListener('livewire:init', function() {
            Livewire.on('openModal', () => {
                const modal = new bootstrap.Modal(document.getElementById('modalTambah'));
                modal.show();
            });

            Livewire.on('closeModal', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalEdit'));
                modal.hide();
            });

            Livewire.on('updateAlertToast', (event) => {
                const data = event;
                swal({
                    title: "Berhasil"
                    , text: "Data berhasil diperbarui"
                    , icon: "success"
                    , buttons: {
                        confirm: {
                            text: "Ok"
                            , value: true
                            , visible: true
                            , className: "btn btn-success"
                            , closeModal: true
                        }
                    }
                    , timer: 2000
                    , timerProgressBar: true
                });
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalEdit'));
                modal.hide();

                setTimeout(function() { window.location.reload(); }, 2000);

            });

            Livewire.on('tambahAlertToast', (event) => {
                const data = event;
                swal({
                    title: "Berhasil"
                    , text: "Data berhasil ditambahkan"
                    , icon: "success"
                    , buttons: {
                        confirm: {
                            text: "Ok"
                            , value: true
                            , visible: true
                            , className: "btn btn-success"
                            , closeModal: true
                        }
                    }
                    , timer: 2000
                    , timerProgressBar: true
                });
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalTambah'));
                modal.hide();

                setTimeout(function() { window.location.reload(); }, 2000);

            });

            Livewire.on('deleteAlertToast', (event) => {
                const data = event;
                swal({
                    title: "Berhasil"
                    , text: "Data berhasil dihapus"
                    , icon: "success"
                    , buttons: {
                        confirm: {
                            text: "Ok"
                            , value: true
                            , visible: true
                            , className: "btn btn-success"
                            , closeModal: true
                        }
                    }
                    , timer: 2000
                    , timerProgressBar: true
                });
                setTimeout(function() { window.location.reload(); }, 2000);

            });

            Livewire.on('errorAlertToast', (event) => {
                const data = event;
                swal({
                    title: "Error"
                    , text: "Terjadi kesalahan"
                    , icon: "error"
                    , buttons: {
                        confirm: {
                            text: "Ok"
                            , value: true
                            , visible: true
                            , className: "btn btn-danger"
                            , closeModal: true
                        }
                    }
                    , timer: 2000
                    , timerProgressBar: true
                });
            });
        });

    </script>
    @endpush
</div>
