<x-layouts.app
    :title="'Meetings'"
    :heading="'Meetings'"
    :subheading="'Search and revisit recordings, transcripts, and decisions.'"
>
    <section class="surface">
        <form action="{{ route('meetings.index') }}" method="GET" class="filter-bar" role="search">
            <div class="min-w-0 flex-1">
                <label class="sr-only" for="meeting-search">Search meetings</label>
                <input id="meeting-search" type="search" name="search" value="{{ request('search') }}" placeholder="Search titles, categories, or transcript" class="field-input">
            </div>
            <div class="w-40">
                <label class="sr-only" for="meeting-category">Filter by category</label>
                <select id="meeting-category" name="category" class="field-input">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-40">
                <label class="sr-only" for="meeting-status">Filter by status</label>
                <select id="meeting-status" name="status" class="field-input">
                    <option value="">All statuses</option>
                    @foreach (['queued', 'processing', 'completed', 'failed'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="button button-secondary"><x-icon name="filter" class="h-4 w-4" /> Apply filters</button>
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
                    <div class="meeting-row-side">{{ $meeting->created_at->format('M j, Y · g:i A') }}</div>
                </a>
            @empty
                <div class="empty-state">
                    <span class="empty-state-icon"><x-icon name="meetings" /></span>
                    <h3>{{ request()->hasAny(['search', 'category', 'status']) ? 'No matching meetings' : 'No meetings yet' }}</h3>
                    <p>{{ request()->hasAny(['search', 'category', 'status']) ? 'Adjust your filters or clear your search to see other meetings.' : 'Upload your first recording to start building your meeting library.' }}</p>
                    @unless(request()->hasAny(['search', 'category', 'status']))
                        <a href="{{ route('meetings.create') }}" class="button button-primary mt-4"><x-icon name="plus" class="h-4 w-4" /> Upload a meeting</a>
                    @endunless
                </div>
            @endforelse
        </div>

        @if ($meetings->hasPages())
            <div class="pagination-wrap">{{ $meetings->links() }}</div>
        @endif
    </section>
</x-layouts.app>
