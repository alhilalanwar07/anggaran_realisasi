<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::view('user', 'user')
    ->middleware(['auth'])
    ->name('user');

Route::view('anggaran', 'anggaran')
    ->middleware(['auth'])
    ->name('anggaran');

Route::view('realisasi', 'realisasi')
    ->middleware(['auth'])
    ->name('realisasi');

    Route::middleware(['auth'])->name('masters.')->group(function () {
        Route::view('urusan', 'urusan')->name('urusan');
        Route::view('urusan-pelaksana', 'urusan-pelaksana')->name('urusan-pelaksana');
        Route::view('skpd', 'skpd')->name('skpd');
        Route::view('sub-skpd', 'sub-skpd')->name('sub-skpd');
        Route::view('program', 'program')->name('program');
        Route::view('kegiatan', 'kegiatan')->name('kegiatan');
        Route::view('sub-kegiatan', 'sub-kegiatan')->name('sub-kegiatan');
        Route::view('akun', 'akun')->name('akun');
        Route::view('kelompok-akun', 'kelompok-akun')->name('akuns.kelompok-akun');
        Route::view('jenis-akun', 'jenis-akun')->name('akuns.jenis-akun');
        Route::view('obyek-akun', 'obyek-akun')->name('akuns.obyek-akun');
        Route::view('rincian-obyek-akun', 'rincian-obyek-akun')->name('akuns.rincian-obyek-akun');
        Route::view('sub-rincian-obyek-akun', 'sub-rincian-obyek-akun')->name('akuns.sub-rincian-obyek-akun');
    });


require __DIR__.'/auth.php';
