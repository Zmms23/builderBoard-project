<x-filament-widgets::widget>
    <x-filament::section
        :heading="__('dashboard.payment_channels.heading')"
        :description="__('dashboard.payment_channels.description')"
    >
        <div class="grid gap-3 lg:grid-cols-2">
            <div class="rounded-xl border border-success-500/20 bg-success-500/5 px-4 py-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-gray-200">{{ __('dashboard.payment_channels.cash') }}</p>
                        <p class="mt-1 text-xs leading-5 text-gray-400">
                            {{ __('dashboard.payment_channels.cash_description') }}
                        </p>
                    </div>

                    <span @class([
                        'rounded-lg px-2 py-1 text-xs font-semibold',
                        'bg-success-500/10 text-success-300' => $cashPaymentsEnabled,
                        'bg-gray-500/10 text-gray-300' => ! $cashPaymentsEnabled,
                    ])>
                        {{ $cashPaymentsEnabled ? __('dashboard.payment_channels.enabled') : __('dashboard.payment_channels.disabled') }}
                    </span>
                </div>
            </div>

            <div @class([
                'rounded-xl border px-4 py-3',
                'border-info-500/20 bg-info-500/5' => $bankTransferEnabled && $hasBankDetails,
                'border-warning-500/20 bg-warning-500/5' => $bankTransferEnabled && ! $hasBankDetails,
                'border-gray-500/20 bg-gray-500/5' => ! $bankTransferEnabled,
            ])>
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-gray-200">{{ __('dashboard.payment_channels.bank_transfer') }}</p>
                        <p class="mt-1 text-xs leading-5 text-gray-400">
                            {{ $hasBankDetails ? __('dashboard.payment_channels.bank_ready') : __('dashboard.payment_channels.missing_bank_details') }}
                        </p>
                    </div>

                    <span @class([
                        'rounded-lg px-2 py-1 text-xs font-semibold',
                        'bg-info-500/10 text-info-300' => $bankTransferEnabled && $hasBankDetails,
                        'bg-warning-500/10 text-warning-300' => $bankTransferEnabled && ! $hasBankDetails,
                        'bg-gray-500/10 text-gray-300' => ! $bankTransferEnabled,
                    ])>
                        {{ $bankTransferEnabled ? __('dashboard.payment_channels.enabled') : __('dashboard.payment_channels.disabled') }}
                    </span>
                </div>

                @if ($bankTransferEnabled)
                    <dl class="mt-4 grid gap-2 text-xs leading-5 text-gray-400 sm:grid-cols-2">
                        <div>
                            <dt class="font-medium text-gray-300">{{ __('dashboard.payment_channels.bank_name') }}</dt>
                            <dd>{{ $bankName ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-300">{{ __('dashboard.payment_channels.account_name') }}</dt>
                            <dd>{{ $bankAccountName ?: '-' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="font-medium text-gray-300">{{ __('dashboard.payment_channels.account_number') }}</dt>
                            <dd class="break-all font-semibold text-gray-100">{{ $bankAccountNumber ?: '-' }}</dd>
                        </div>
                    </dl>

                    @if (filled($paymentInstructions))
                        <p class="mt-3 rounded-lg border border-white/10 bg-black/10 px-3 py-2 text-xs leading-5 text-gray-300">
                            {{ $paymentInstructions }}
                        </p>
                    @endif
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
