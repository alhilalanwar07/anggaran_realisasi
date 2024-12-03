<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {

    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <div class="sidebar" data-background-color="white">
        <div class="sidebar-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark">
                <a href="#" class="logo">
                    <img src="{{ url('/') }}/assets/img/logo/koltim-2.png" alt="kolaka timur" height="30" />
                </a>
                <div class="nav-toggle">
                    <button class="btn btn-toggle toggle-sidebar">
                        <i class="gg-menu-right"></i>
                    </button>
                    <button class="btn btn-toggle sidenav-toggler">
                        <i class="gg-menu-left"></i>
                    </button>
                </div>
                <button class="topbar-toggler more">
                    <i class="gg-more-vertical-alt"></i>
                </button>
            </div>
            <!-- End Logo Header -->
        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
            <div class="sidebar-content">
                <ul class="nav nav-secondary">
                    <li class="nav-item {{ Route::is('dashboard') ? 'active text-info' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard') }}" wire:navigate>
                            <i class="fas fa-home"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">Masters</h4>
                    </li>
                    <x-nav-collapse title="Data Master SKPD" icon="fas fa-database" href="#masters-skpd" active="{{ Route::is('masters.urusan') || Route::is('masters.urusan-pelaksana') || Route::is('masters.skpd') || Route::is('masters.sub-skpd') || Route::is('masters.program') || Route::is('masters.kegiatan') || Route::is('masters.sub-kegiatan') }}">
                        <x-nav-link href="{{ route('masters.urusan') }}" active="{{ Route::is('masters.urusan') }}">Urusan</x-nav-link>
                        <x-nav-link href="{{ route('masters.urusan-pelaksana') }}" active="{{ Route::is('masters.urusan-pelaksana') }}">Urusan Pelaksana</x-nav-link>
                        <x-nav-link href="{{ route('masters.skpd') }}" active="{{ Route::is('masters.skpd') }}">SKPD</x-nav-link>
                        <x-nav-link href="{{ route('masters.sub-skpd') }}" active="{{ Route::is('masters.sub-skpd') }}">Sub SKPD</x-nav-link>
                        <x-nav-link href="{{ route('masters.program') }}" active="{{ Route::is('masters.program') }}">Program</x-nav-link>
                        <x-nav-link href="{{ route('masters.kegiatan') }}" active="{{ Route::is('masters.kegiatan') }}">Kegiatan</x-nav-link>
                        <x-nav-link href="{{ route('masters.sub-kegiatan') }}" active="{{ Route::is('masters.sub-kegiatan') }}">Sub Kegiatan</x-nav-link>
                    </x-nav-collapse>

                    <x-nav-collapse title="Data Master Akun" icon="fas fa-folder" href="#masters-akun" active="{{ Route::is('masters.akun') || Route::is('masters.akuns.*') }}">
                        <x-nav-link href="{{ route('masters.akun') }}" active="{{ Route::is('masters.akun') }}">Akun</x-nav-link>
                        <x-nav-link href="{{ route('masters.akuns.kelompok-akun') }}" active="{{ Route::is('masters.akuns.kelompok-akun') }}">Kelompok Akun</x-nav-link>
                        <x-nav-link href="{{ route('masters.akuns.jenis-akun') }}" active="{{ Route::is('masters.akuns.jenis-akun') }}">Jenis Akun</x-nav-link>
                        <x-nav-link href="{{ route('masters.akuns.obyek-akun') }}" active="{{ Route::is('masters.akuns.obyek-akun') }}">Obyek Akun</x-nav-link>
                        <x-nav-link href="{{ route('masters.akuns.rincian-obyek-akun') }}" active="{{ Route::is('masters.akuns.rincian-obyek-akun') }}">Rincian Obyek Akun</x-nav-link>
                        <x-nav-link href="{{ route('masters.akuns.sub-rincian-obyek-akun') }}" active="{{ Route::is('masters.akuns.sub-rincian-obyek-akun') }}">Sub Rincian Obyek Akun</x-nav-link>
                    </x-nav-collapse>
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">Proses</h4>
                    </li>
                    <li class="nav-item {{ Route::is('anggaran') ? 'active text-info' : '' }}">
                        <a class="nav-link" href="{{ route('anggaran') }}" wire:navigate>
                            <i class="fas fa-file-invoice-dollar"></i>
                            <p>Anggaran</p>
                        </a>
                    </li>
                    <li class="nav-item {{ Route::is('realisasi') ? 'active text-info' : '' }}">
                        <a class="nav-link" href="{{ route('realisasi') }}" wire:navigate>
                            <i class="fas fa-chart-line"></i>
                            <p>Realisasi</p>
                        </a>
                    </li>
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">Output</h4>
                    </li>
                    <li class="nav-item {{ Route::is('laporan') ? 'active text-info' : '' }}">
                        <a class="nav-link" href="{{ route('laporan') }}" wire:navigate>
                            <i class="fas fa-print"></i>
                            <p>Laporan</p>
                        </a>
                    </li>
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">Settings</h4>
                    </li>
                    <li class="nav-item {{ Route::is('user') ? 'active text-info' : '' }}">
                        <a class="nav-link" href="{{ route('user') }}" wire:navigate>
                            <i class="fas fa-users"></i>
                            <p>Manajemen User</p>
                        </a>
                    </li>
                    <li class="nav-item {{ Route::is('profile') ? 'active text-info' : '' }}">
                        <a class="nav-link" href="{{ route('profile') }}" wire:navigate>
                            <i class="fas fa-user"></i>
                            <p>Profil</p>
                        </a>
                    </li>


                    <br>
                    <div class="px-4">
                        <li class="nav-item" style="padding: 0px !important;">
                            <a href="#" wire:click="logout" class=" text-center btn btn-sm btn-danger w-100 btn-block d-flex justify-content-center align-items-center" style="padding: 0px !important;">
                                <i class="fas fa-sign-out-alt fa-lg m-2 p-1"></i> &nbsp;
                                <p style="padding: 0px !important; margin: 5px !important">Keluar</p>
                            </a>
                        </li>
                    </div>
                </ul>
            </div>
        </div>
    </div>
</div>
