<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="page-inner">
            <div class="row justify-content-center">
                <div class="col-md-12 space-y-6">
                    <div class="card p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <div class="card-body">
                            <livewire:profile.update-profile-information-form />
                        </div>
                    </div>

                    <div class="card p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <div class="card-body">
                            <livewire:profile.update-password-form />
                        </div>
                    </div>

                    <div class="card p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <div class="card-body">
                            <livewire:profile.delete-user-form />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
