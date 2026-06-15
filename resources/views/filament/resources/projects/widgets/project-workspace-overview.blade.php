<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
    <div class="border-b border-gray-200 px-6 py-5 dark:border-white/10">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase text-primary-600 dark:text-primary-400">
                    {{ __('project.workspace.eyebrow') }}
                </p>
                <h2 class="mt-2 text-2xl font-bold text-gray-950 dark:text-white">
                    {{ $project->title }}
                </h2>
                <p class="mt-2 text-sm leading-6 text-gray-500 dark:text-gray-400">
                    {{ __('project.workspace.description') }}
                </p>
            </div>

            <div class="flex min-w-52 flex-col gap-2">
                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-700 dark:bg-white/10 dark:text-gray-200">
                        {{ $project->status?->getLabel() ?? '-' }}
                    </span>
                    <span class="rounded-full bg-primary-50 px-3 py-1 text-sm font-medium text-primary-700 dark:bg-primary-500/10 dark:text-primary-300">
                        {{ __('project.workspace.progress_value', ['progress' => $project->progress]) }}
                    </span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-white/10">
                    <div class="h-full rounded-full bg-primary-500" style="width: {{ max(0, min(100, (int) $project->progress)) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid divide-y divide-gray-200 dark:divide-white/10 lg:grid-cols-4 lg:divide-x lg:divide-y-0">
        <div class="px-6 py-5">
            <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                {{ __('project.workspace.client') }}
            </p>
            <p class="mt-2 text-base font-semibold text-gray-950 dark:text-white">
                {{ $client?->name ?? '-' }}
            </p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $client?->email ?? $client?->phone ?? __('project.workspace.no_client_contact') }}
            </p>
        </div>

        <div class="px-6 py-5">
            <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                {{ __('project.workspace.orders') }}
            </p>
            <p class="mt-2 text-base font-semibold text-gray-950 dark:text-white">
                {{ __('project.workspace.orders_summary', ['open' => $openOrdersCount, 'total' => $ordersCount]) }}
            </p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if ($nextOrder)
                    {{ __('project.workspace.next_order', ['order' => $nextOrder->number, 'deadline' => $nextOrder->deadline?->format('Y-m-d') ?? '-']) }}
                @else
                    {{ __('project.workspace.no_open_orders') }}
                @endif
            </p>
        </div>

        <div class="px-6 py-5">
            <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                {{ __('project.workspace.timeline') }}
            </p>
            <p class="mt-2 text-base font-semibold text-gray-950 dark:text-white">
                {{ __('project.workspace.timeline_summary', ['done' => $timelineCompleted, 'total' => $timelineTotal]) }}
            </p>
            <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-white/10">
                <div class="h-full rounded-full bg-success-500" style="width: {{ $timelinePercent }}%"></div>
            </div>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ __('project.workspace.deadline', ['deadline' => $project->deadline?->format('Y-m-d') ?? '-']) }}
            </p>
        </div>

        <div class="px-6 py-5">
            <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                {{ __('project.workspace.client_updates') }}
            </p>
            <p class="mt-2 text-base font-semibold text-gray-950 dark:text-white">
                {{ __('project.workspace.visible_proofs', ['count' => $visibleProofsCount]) }}
            </p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if ($latestProof)
                    {{ __('project.workspace.latest_proof', ['proof' => $latestProof->title]) }}
                @else
                    {{ __('project.workspace.no_proofs') }}
                @endif
            </p>
        </div>
    </div>

    <div class="grid gap-0 border-t border-gray-200 dark:border-white/10 xl:grid-cols-5">
        @if ($settings->budget_tracking_enabled)
            <div class="border-b border-gray-200 px-6 py-5 dark:border-white/10 xl:col-span-3 xl:border-b-0 xl:border-r">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                            {{ __('project.workspace.estimated_total') }}
                        </p>
                        <p class="mt-1 text-lg font-bold text-gray-950 dark:text-white">{{ $estimatedTotal }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                            {{ __('project.workspace.paid_total') }}
                        </p>
                        <p class="mt-1 text-lg font-bold text-success-600 dark:text-success-400">{{ $paidTotal }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                            {{ __('project.workspace.remaining_total') }}
                        </p>
                        <p class="mt-1 text-lg font-bold text-warning-600 dark:text-warning-400">{{ $remainingTotal }}</p>
                    </div>
                </div>

                <div class="mt-5">
                    <div class="flex items-center justify-between text-xs font-medium text-gray-500 dark:text-gray-400">
                        <span>{{ __('project.workspace.budget_received') }}</span>
                        <span>{{ $budgetSpentPercent }}%</span>
                    </div>
                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-white/10">
                        <div class="h-full rounded-full bg-success-500" style="width: {{ $budgetSpentPercent }}%"></div>
                    </div>
                </div>
            </div>
        @endif

        <div class="px-6 py-5 xl:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                        {{ __('project.workspace.readiness.title') }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('project.workspace.readiness.description') }}
                    </p>
                </div>
                <span class="rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700 dark:bg-primary-500/10 dark:text-primary-300">
                    {{ __('project.workspace.active_services', ['count' => $activeServicesCount]) }}
                </span>
            </div>

            <div class="mt-4 space-y-3">
                @foreach ($readinessItems as $item)
                    <div class="flex gap-3">
                        <span @class([
                            'mt-1 size-2.5 rounded-full',
                            'bg-success-500' => $item['complete'],
                            'bg-warning-500' => ! $item['complete'],
                        ])></span>
                        <div>
                            <p class="text-sm font-semibold text-gray-950 dark:text-white">{{ $item['label'] }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
