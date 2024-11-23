<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h2 class="text-2xl font-bold mb-2 text-left">SIUPD</h2>
    <p class="text-gray-800 mb-8 text-m text-left">Mohon masukkan informasi akun Anda untuk mulai menggunakan SIUPD</p>
    <br>
    <p></p>
    <br>
    <form wire:submit="login" class="w-full max-w-md">

        <div class="mb-4">
            <label for="email" class="block fw-semibold text-gray-500 mb-1">E-mail</label>
            <input wire:model="form.email" type="email" id="email" class="w-full p-2 border rounded" placeholder="" required>
            <x-input-error :messages="$errors->get('form.email')" class="mt-1" />
        </div>

        <div class="mb-6">
            <label for="password" class="block fw-semibold text-gray-500 mb-1">Kata Sandi</label>
            <input wire:model="form.password" type="password" id="password" class="w-full p-2 border rounded" placeholder="" required>
            <x-input-error :messages="$errors->get('form.password')" class="mt-1" />
        </div>

        <x-button type="submit" full-width >
            Masuk Ke Sistem
        </x-button>
        <br>
        <br>
        <br>
    </form>
</div>
