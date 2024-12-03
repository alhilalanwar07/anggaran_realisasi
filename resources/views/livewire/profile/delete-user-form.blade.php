<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <!-- Usage in delete-user-form.blade.php -->
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirm-user-deletion">
        {{ __('Delete Account') }}
    </button>

    <x-modal name="confirm-user-deletion" maxWidth="md" title="{{ __('Delete Account') }}">
        <form wire:submit.prevent="deleteUser" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6 mb-3">
                <label for="password" class="sr-only">{{ __('Password') }}</label>
                <input wire:model="password" id="password" name="password" type="password" class="form-control mt-1 block w-3/4" placeholder="{{ __('Password') }}" />
                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="mt-6 d-flex justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="btn btn-danger ms-3">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>

    @push('script')
    <script>
        // Initialize Bootstrap modals
        document.addEventListener('livewire:initialized', () => {
            const myModalEl = document.getElementById('confirm-user-deletion')
            const modal = new bootstrap.Modal(myModalEl)
        });

    </script>
    @endpush
</section>
