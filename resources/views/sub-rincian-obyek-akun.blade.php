<x-admin-layout>
    @section('title', 'Sub Rincian Obyek Akun')
    <div class="page-inner">
        <div class="page-header">
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Data Master</a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Sub Rincian Obyek Akun</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <livewire:admin.sub-rincian-obyek-akun />
        </div>
    </div>
</x-admin-layout>
