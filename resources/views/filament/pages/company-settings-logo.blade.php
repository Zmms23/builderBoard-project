<div class="grid gap-6 lg:grid-cols-[220px,1fr]">
    <div class="flex flex-col gap-3">
        @if ($currentLogoUrl)
            <img
                src="{{ $currentLogoUrl }}"
                alt="{{ __('settings.fields.logo') }}"
                class="h-36 w-full rounded-xl border border-gray-200 object-contain p-4 dark:border-white/10"
            >
        @else
            <div class="flex h-36 items-center justify-center rounded-xl border border-dashed border-gray-300 text-sm text-gray-500 dark:border-white/10 dark:text-gray-400">
                {{ __('settings.states.no_logo') }}
            </div>
        @endif
    </div>

    <div class="space-y-4">
        <form action="{{ route('admin.company-settings.logo.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf

            <div>
                <label for="company-logo" class="mb-2 block text-sm font-medium text-gray-950 dark:text-white">
                    {{ __('settings.fields.logo') }}
                </label>

                <input
                    id="company-logo"
                    name="logo"
                    type="file"
                    accept=".jpg,.jpeg,.png,.webp,.svg"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-950 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-sm file:font-medium dark:border-white/10 dark:bg-gray-800 dark:text-white dark:file:bg-gray-700"
                >

                @error('logo')
                    <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex flex-wrap gap-3">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-500"
                >
                    {{ __('settings.actions.upload_logo') }}
                </button>

                @if ($currentLogoUrl)
                    <button
                        type="submit"
                        form="remove-company-logo"
                        class="inline-flex items-center justify-center rounded-lg border border-danger-300 px-4 py-2 text-sm font-medium text-danger-600 hover:bg-danger-50 dark:border-danger-500/30 dark:text-danger-400 dark:hover:bg-danger-500/10"
                    >
                        {{ __('settings.actions.remove_logo') }}
                    </button>
                @endif
            </div>
        </form>

        @if ($currentLogoUrl)
            <form id="remove-company-logo" action="{{ route('admin.company-settings.logo.destroy') }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
</div>
