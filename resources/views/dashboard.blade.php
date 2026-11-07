<x-layouts.app
    :title="'Overview'"
    :heading="'Overview'"
    :subheading="'A clear view of your meeting intelligence.'"
>
    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="Workspace statistics">
        <article class="metric-card">
            <span class="metric-icon"><x-icon name="meetings" /></span>
            <div>
                <p class="metric-label">Meetings</p>
                <p class="metric-value">{{ number_format($stats['total']) }}</p>
                <p class="metric-hint">Available in your workspace</p>
            </div>
        </article>
        <article class="metric-card">
            <span class="metric-icon"><x-icon name="clock" /></span>
            <div>
                <p class="metric-label">In progress</p>
                <p class="metric-value">{{ number_format($stats['processing']) }}</p>
                <p class="metric-hint">Queued or currently processing</p>
            </div>
        </article>
        <article class="metric-card">
            <span class="metric-icon"><x-icon name="check" /></span>
            <div>
                <p class="metric-label">Completed notes</p>
                <p class="metric-value">{{ number_format($stats['completed']) }}</p>
                <p class="metric-hint">Ready to review and share</p>
            </div>
        </article>
        <article class="metric-card">
            <span class="metric-icon"><x-icon name="tasks" /></span>
            <div>
                <p class="metric-label">Action items</p>
                <p class="metric-value">{{ number_format($stats['action_items']) }}</p>
                <p class="metric-hint">Extracted from meeting notes</p>
            </div>
        </article>
    </section>

    <section class="dashboard-grid">
        <div class="dashboard-primary">
            <div class="surface">
                <div class="section-heading">
                    <div>
                        <h2>Recent meetings</h2>
                        <p>Your latest recordings and processing updates</p>
                    </div>
                    <a href="{{ route('meetings.index') }}" class="section-link">All meetings <x-icon name="chevron" class="h-3.5 w-3.5" /></a>
                </div>
                <form action="{{ route('dashboard') }}" method="GET" class="filter-bar" role="search">
                    <div class="min-w-0 flex-1">
                        <label class="sr-only" for="dashboard-search">Search meetings</label>
                        <input id="dashboard-search" type="search" name="search" value="{{ request('search') }}" placeholder="Search title, category, or transcript" class="field-input">
                    </div>
                    <div class="w-40">
                        <label class="sr-only" for="dashboard-status">Filter by processing status</label>
                        <select id="dashboard-status" name="status" class="field-input">
                            <option value="">All statuses</option>
                            @foreach (['queued', 'processing', 'completed', 'failed'] as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="button button-secondary" type="submit"><x-icon name="filter" class="h-4 w-4" /> Filter</button>
                </form>

                <div>
                    @forelse ($meetings as $meeting)
                        <a href="{{ route('meetings.show', $meeting) }}" class="meeting-row">
                            <div>
                                <div class="meeting-card-heading">
                                    <h3>{{ $meeting->title }}</h3>
                                    <span @class([
                                        'status-pill',
                                        'bg-emerald-50 text-emerald-700' => $meeting->status === 'completed',
                                        'bg-rose-50 text-rose-700' => $meeting->status === 'failed',
                                        'bg-amber-50 text-amber-700' => in_array($meeting->status, ['queued', 'processing']),
                                    ])>{{ $meeting->status }}</span>
                                </div>
                                <div class="meeting-meta">
                                    <span>{{ $meeting->category ?: 'General' }}</span>
                                    <span class="meeting-meta-dot">·</span>
                                    <span>{{ $meeting->media_name }}</span>
                                    @if ($meeting->team)
                                        <span class="meeting-meta-dot">·</span>
                                        <span>{{ $meeting->team->name }}</span>
                                    @endif
                                </div>
                                @if ($meeting->tags->isNotEmpty())
                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                        @foreach ($meeting->tags as $tag)
                                            <span class="tag-chip">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="meeting-row-side">{{ $meeting->created_at->diffForHumans() }}</div>
                        </a>
                    @empty
                        <div class="empty-state">
                            <span class="empty-state-icon"><x-icon name="meetings" /></span>
                            <h3>{{ request()->filled('search') || request()->filled('status') ? 'No matching meetings' : 'Your meeting library starts here' }}</h3>
                            <p>{{ request()->filled('search') || request()->filled('status') ? 'Try adjusting your search or filters.' : 'Upload a recording to create a transcript, summary, decisions, and action items.' }}</p>
                            @unless(request()->filled('search') || request()->filled('status'))
                                <a href="{{ route('meetings.create') }}" class="button button-primary mt-4"><x-icon name="plus" class="h-4 w-4" /> Upload a meeting</a>
                            @endunless
                        </div>
                    @endforelse
                </div>
                @if ($meetings->hasPages())
                    <div class="pagination-wrap">{{ $meetings->links() }}</div>
                @endif
            </div>

            <div class="surface">
                <div class="section-heading">
                    <div>
                        <h2>Action items</h2>
                        <p>Latest tasks extracted from your meetings</p>
                    </div>
                    <span class="tag-chip">{{ number_format($stats['action_items']) }} total</span>
                </div>
                @forelse ($latestActionItems as $item)
                    <a href="{{ route('meetings.show', $item->meeting) }}" class="task-row">
                        <span class="task-check"><x-icon name="check" class="h-3 w-3" /></span>
                        <span class="task-description">
                            {{ $item->description }}
                            <span class="task-context">
                                {{ $item->meeting->title }} · {{ $item->assignee_name ?: 'Unassigned' }}
                                @if ($item->due_date)
                                    · Due {{ $item->due_date->format('M j, Y') }}
                                @endif
                            </span>
                        </span>
                    </a>
                @empty
                    <div class="px-5 pb-5 text-xs text-slate-500">Action items will appear here after meetings are processed.</div>
                @endforelse
            </div>
        </div>

        <aside class="dashboard-aside">
            <div class="surface">
                <div class="section-heading">
                    <div>
                        <h2>Notifications</h2>
                        <p>Meeting processing updates</p>
                    </div>
                    <x-icon name="bell" class="h-4 w-4 text-slate-400" />
                </div>
                @forelse ($notifications as $notification)
                    <a href="{{ $notification->data['url'] ?? route('dashboard') }}" class="task-row">
                        <span class="notification-icon"><x-icon name="check" class="h-4 w-4" /></span>
                        <span class="task-description">
                            {{ $notification->data['meeting_title'] ?? 'Meeting update' }}
                            <span class="task-context">Status: {{ $notification->data['status'] ?? 'updated' }}</span>
                        </span>
                    </a>
                @empty
                    <p class="px-5 pb-5 text-xs text-slate-500">You're all caught up. New meeting updates will show here.</p>
                @endforelse
            </div>

            <div class="surface">
                <div class="section-heading">
                    <div>
                        <h2>Your teams</h2>
                        <p>Teams you currently belong to</p>
                    </div>
                </div>
                @forelse ($teams as $team)
                    <div class="task-row">
                        <span class="workspace-avatar">{{ str($team->name)->substr(0, 1)->upper() }}</span>
                        <span class="task-description">
                            {{ $team->name }}
                            <span class="task-context">{{ $team->pivot->role ?? 'member' }}</span>
                        </span>
                    </div>
                @empty
                    <p class="px-5 pb-5 text-xs text-slate-500">No team memberships are available.</p>
                @endforelse
            </div>
        </aside>
    </section>
</x-layouts.app>
