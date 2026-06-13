<x-filament-widgets::widget>
    <x-filament::section
        :heading="__('dashboard.features.heading')"
        :description="__('dashboard.features.description')"
    >
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
            @foreach ($features as $feature)
                <div @class([
                    'rounded-xl border px-4 py-3',
                    'border-success-500/20 bg-success-500/5' => $feature['enabled'],
                    'border-gray-500/20 bg-gray-500/5' => ! $feature['enabled'],
                ])>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-200">{{ $feature['label'] }}</p>
                            <p class="mt-1 text-xs leading-5 text-gray-400">{{ $feature['description'] }}</p>
                        </div>

                        <span @class([
                            'rounded-lg px-2 py-1 text-xs font-semibold',
                            'bg-success-500/10 text-success-300' => $feature['enabled'],
                            'bg-gray-500/10 text-gray-300' => ! $feature['enabled'],
                        ])>
                            {{ $feature['enabled'] ? __('dashboard.features.enabled') : __('dashboard.features.disabled') }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
