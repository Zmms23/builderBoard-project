<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-white/10">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-primary-600 dark:text-primary-400">
                    {{ __('project.workspace.eyebrow') }}
                </p>
                <h2 class="mt-1 text-xl font-bold text-gray-950 dark:text-white">
                    {{ $project->title }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('project.workspace.description') }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-700 dark:bg-white/10 dark:text-gray-200">
                    {{ $project->status?->getLabel() ?? '-' }}
                </span>
                <span class="rounded-full bg-primary-50 px-3 py-1 text-sm font-medium text-primary-700 dark:bg-primary-500/10 dark:text-primary-300">
                    {{ $project->progress }}%
                </span>
            </div>
        </div>
    </div>

    <div class="grid gap-4 p-6 lg:grid-cols-4">
        <div class="rounded-lg border border-gray-200 p-4 dark:border-white/10">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                {{ __('project.workspace.client') }}
            </p>
            <p class="mt-2 text-base font-semibold text-gray-950 dark:text-white">
                {{ $client?->name ?? '-' }}
            </p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $client?->email ?? $client?->phone ?? __('project.workspace.no_client_contact') }}
            </p>
        </div>

        <div class="rounded-lg border border-gray-200 p-4 dark:border-white/10">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
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

        <div class="rounded-lg border border-gray-200 p-4 dark:border-white/10">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                {{ __('project.workspace.timeline') }}
            </p>
            <p class="mt-2 text-base font-semibold text-gray-950 dark:text-white">
                {{ __('project.workspace.timeline_summary', ['done' => $timelineCompleted, 'total' => $timelineTotal]) }}
            </p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('project.workspace.deadline', ['deadline' => $project->deadline?->format('Y-m-d') ?? '-']) }}
            </p>
        </div>

        <div class="rounded-lg border border-gray-200 p-4 dark:border-white/10">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
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

    @if ($settings->budget_tracking_enabled)
        <div class="grid gap-4 border-t border-gray-200 px-6 py-4 dark:border-white/10 lg:grid-cols-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('project.workspace.estimated_total') }}
                </p>
                <p class="mt-1 text-lg font-bold text-gray-950 dark:text-white">{{ $estimatedTotal }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('project.workspace.paid_total') }}
                </p>
                <p class="mt-1 text-lg font-bold text-success-600 dark:text-success-400">{{ $paidTotal }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('project.workspace.remaining_total') }}
                </p>
                <p class="mt-1 text-lg font-bold text-warning-600 dark:text-warning-400">{{ $remainingTotal }}</p>
            </div>
        </div>
    @endif
</div>
