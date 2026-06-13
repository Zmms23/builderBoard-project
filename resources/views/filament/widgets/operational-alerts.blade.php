<x-filament-widgets::widget>
    <x-filament::section
        :heading="__('dashboard.alerts.heading')"
        :description="__('dashboard.alerts.description')"
    >
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($alerts as $alert)
                <div
                    @class([
                        'rounded-xl border px-4 py-3',
                        'border-danger-500/20 bg-danger-500/5' => $alert['color'] === 'danger',
                        'border-warning-500/20 bg-warning-500/5' => $alert['color'] === 'warning',
                        'border-success-500/20 bg-success-500/5' => $alert['color'] === 'success',
                        'border-gray-500/20 bg-gray-500/5' => $alert['color'] === 'gray',
                    ])
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-200">{{ $alert['label'] }}</p>
                            <p class="mt-1 text-xs leading-5 text-gray-400">{{ $alert['description'] }}</p>
                        </div>

                        <span
                            @class([
                                'rounded-lg px-2 py-1 text-sm font-semibold',
                                'bg-danger-500/10 text-danger-300' => $alert['color'] === 'danger',
                                'bg-warning-500/10 text-warning-300' => $alert['color'] === 'warning',
                                'bg-success-500/10 text-success-300' => $alert['color'] === 'success',
                                'bg-gray-500/10 text-gray-300' => $alert['color'] === 'gray',
                            ])
                        >
                            {{ $alert['value'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
