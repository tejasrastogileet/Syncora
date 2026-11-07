<x-layouts.app
    :title="'Analytics'"
    :heading="'Analytics'"
    :subheading="'A live overview of platform activity.'"
>
    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="Platform totals">
        @foreach ($totals as $label => $value)
            <article class="metric-card">
                <span class="metric-icon"><x-icon :name="$label === 'users' ? 'user' : ($label === 'meetings' ? 'meetings' : ($label === 'completed' ? 'check' : 'alert'))" /></span>
                <div>
                    <p class="metric-label">{{ str($label)->headline() }}</p>
                    <p class="metric-value">{{ number_format($value) }}</p>
                    <p class="metric-hint">Current platform total</p>
                </div>
            </article>
        @endforeach
    </section>

    <section class="mt-4 grid items-start gap-4 xl:grid-cols-2">
        <div class="surface">
            <div class="section-heading">
                <div><h2>Meetings by category</h2><p>Distribution across recorded meetings</p></div>
                <x-icon name="analytics" class="h-4 w-4 text-slate-400" />
            </div>
            @forelse ($categoryBreakdown as $item)
                <div class="task-row justify-between">
                    <span class="task-description">{{ $item->category ?: 'Uncategorized' }}</span>
                    <span class="tag-chip">{{ number_format($item->aggregate) }}</span>
                </div>
            @empty
                <div class="empty-state">
                    <h3>No category data yet</h3>
                    <p>Meeting categories will appear here as recordings are added.</p>
                </div>
            @endforelse
        </div>

        <div class="surface">
            <div class="section-heading">
                <div><h2>Recent processing failures</h2><p>Meetings that may need attention</p></div>
                <x-icon name="alert" class="h-4 w-4 text-slate-400" />
            </div>
            @forelse ($recentFailures as $meeting)
                <a href="{{ route('meetings.show', $meeting) }}" class="task-row">
                    <span class="notification-icon bg-rose-50 text-rose-700"><x-icon name="alert" class="h-4 w-4" /></span>
                    <span class="task-description">
                        {{ $meeting->title }}
                        <span class="task-context">{{ \Illuminate\Support\Str::limit($meeting->failure_reason ?: 'Unknown processing error', 110) }}</span>
                    </span>
                </a>
            @empty
                <div class="empty-state">
                    <span class="empty-state-icon"><x-icon name="check" /></span>
                    <h3>No failed meetings</h3>
                    <p>There are no recent processing failures to review.</p>
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.app>
