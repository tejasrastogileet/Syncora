<x-layouts.app
    :title="$meeting->title"
    :heading="$meeting->title"
    :subheading="($meeting->category ?: 'General'). ' · Uploaded '.$meeting->created_at->format('M j, Y')"
>
    <div class="grid items-start gap-4 xl:grid-cols-[minmax(0,1.65fr)_minmax(280px,0.8fr)]">
        <div class="grid min-w-0 gap-4">
            <livewire:meeting-status-panel :meeting="$meeting" :key="'meeting-status-'.$meeting->id" />

            <section class="surface detail-section" id="recording">
                <div class="section-heading">
                    <div>
                        <h2>Recording</h2>
                        <p>{{ $meeting->media_name }} · {{ number_format($meeting->media_size / 1024 / 1024, 1) }} MB</p>
                    </div>
                    <span class="tag-chip">{{ strtoupper(pathinfo($meeting->media_name, PATHINFO_EXTENSION) ?: 'MEDIA') }}</span>
                </div>
                <div class="px-4 pb-4 sm:px-5">
                    <div class="recording-preview">
                        @if (str_starts_with($meeting->media_mime_type, 'video/'))
                            <video class="video-player" controls preload="metadata">
                                <source src="{{ route('meetings.media', $meeting) }}" type="{{ $meeting->media_mime_type }}">
                                Your browser does not support this video format.
                            </video>
                        @else
                            <audio class="audio-player" controls preload="metadata">
                                <source src="{{ route('meetings.media', $meeting) }}" type="{{ $meeting->media_mime_type }}">
                                Your browser does not support audio playback.
                            </audio>
                        @endif
                    </div>
                </div>
            </section>

            <section class="surface detail-section" id="summary">
                <div class="section-heading">
                    <div>
                        <h2>AI summary</h2>
                        <p>Key context and takeaways from this conversation</p>
                    </div>
                    <div class="media-toolbar">
                        <button type="button" class="button button-secondary" data-copy-target="meeting-summary-content"><x-icon name="copy" class="h-4 w-4" /> Copy</button>
                        <a href="{{ route('meetings.export.pdf', $meeting) }}" class="button button-secondary">PDF</a>
                        <a href="{{ route('meetings.export.docx', $meeting) }}" class="button button-secondary">DOCX</a>
                    </div>
                </div>
                <div class="px-5 pb-5">
                    <div id="meeting-summary-content" class="prose-content markdown-content">
                        {!! \Illuminate\Support\Str::markdown($meeting->summary_markdown ?: 'Your meeting summary will appear here after processing is complete.') !!}
                    </div>
                </div>
            </section>

            <section class="surface detail-section" id="transcript">
                <div class="section-heading">
                    <div>
                        <h2>Transcript</h2>
                        <p>{{ $meeting->transcriptSegments->count() }} timestamped segments</p>
                    </div>
                    <button type="button" class="button button-secondary" data-copy-target="meeting-transcript-content"><x-icon name="copy" class="h-4 w-4" /> Copy transcript</button>
                </div>
                <div id="meeting-transcript-content">
                    @forelse ($meeting->transcriptSegments as $segment)
                        <div class="transcript-segment">
                            <div class="transcript-speaker">
                                {{ $segment->speaker_name ?: 'Speaker not identified' }}
                                @if ($segment->start_second !== null)
                                    <span class="transcript-time">{{ gmdate('i:s', (int) $segment->start_second) }}</span>
                                @endif
                            </div>
                            <p class="transcript-text">{{ $segment->text }}</p>
                        </div>
                    @empty
                        <div class="empty-state">
                            <span class="empty-state-icon"><x-icon name="notes" /></span>
                            <h3>Transcript not ready</h3>
                            <p>The transcript will appear here after the recording finishes processing.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="grid min-w-0 gap-4">
            <section class="surface detail-section" id="action-items">
                <div class="section-heading">
                    <div>
                        <h2>Action items</h2>
                        <p>Follow-ups identified in this meeting</p>
                    </div>
                    <span class="tag-chip">{{ $meeting->actionItems->count() }}</span>
                </div>
                @forelse ($meeting->actionItems as $item)
                    <div class="detail-list-item">
                        <p class="font-semibold text-slate-700">{{ $item->description }}</p>
                        <div class="detail-caption">
                            <span>{{ $item->assignee_name ?: 'Unassigned' }}</span>
                            <span>·</span>
                            <span>{{ ucfirst($item->priority) }} priority</span>
                            @if ($item->due_date)
                                <span>·</span>
                                <span>Due {{ $item->due_date->format('M j, Y') }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="px-5 pb-5 text-xs text-slate-500">No action items have been extracted yet.</p>
                @endforelse
            </section>

            <section class="surface detail-section" id="decisions">
                <div class="section-heading">
                    <div>
                        <h2>Decisions</h2>
                        <p>Explicit outcomes from the discussion</p>
                    </div>
                    <span class="tag-chip">{{ $meeting->decisions->count() }}</span>
                </div>
                @forelse ($meeting->decisions as $decision)
                    <div class="detail-list-item">
                        <p class="font-semibold text-slate-700">{{ $decision->decision }}</p>
                        @if ($decision->rationale)
                            <p class="mt-1 text-slate-500">{{ $decision->rationale }}</p>
                        @endif
                        @if ($decision->speaker_name)
                            <p class="detail-caption">{{ $decision->speaker_name }}</p>
                        @endif
                    </div>
                @empty
                    <p class="px-5 pb-5 text-xs text-slate-500">No decisions have been extracted yet.</p>
                @endforelse
            </section>

            <section class="surface detail-section" id="key-points">
                <div class="section-heading">
                    <div>
                        <h2>Key discussion points</h2>
                        <p>Topics raised in the conversation</p>
                    </div>
                </div>
                @forelse ($meeting->key_points ?? [] as $point)
                    <div class="detail-list-item flex gap-2">
                        <span class="mt-1.5 h-1.5 w-1.5 flex-none rounded-full bg-teal-600"></span>
                        <span>{{ $point }}</span>
                    </div>
                @empty
                    <p class="px-5 pb-5 text-xs text-slate-500">Key points will appear after processing.</p>
                @endforelse
            </section>

            <section class="surface">
                <div class="section-heading">
                    <div>
                        <h2>Meeting details</h2>
                        <p>Recording and sharing information</p>
                    </div>
                    @can('update', $meeting)
                        <a href="{{ route('meetings.edit', $meeting) }}" class="section-link">Edit <x-icon name="arrow" class="h-3.5 w-3.5" /></a>
                    @endcan
                </div>
                <dl class="grid gap-3 px-5 pb-5 text-[11px]">
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">Owner</dt><dd class="text-right font-medium text-slate-700">{{ $meeting->owner->name }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">Category</dt><dd class="text-right font-medium text-slate-700">{{ $meeting->category ?: 'General' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">Language</dt><dd class="text-right font-medium text-slate-700">{{ strtoupper($meeting->language) }}</dd></div>
                    @if ($meeting->team)
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Team</dt><dd class="text-right font-medium text-slate-700">{{ $meeting->team->name }}</dd></div>
                    @endif
                    @if ($meeting->tags->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 border-t border-slate-100 pt-3">
                            @foreach ($meeting->tags as $tag)
                                <span class="tag-chip">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </dl>
                @if ($meeting->collaborators->isNotEmpty())
                    <div class="border-t border-slate-100 px-5 py-4">
                        <h3 class="text-[11px] font-semibold text-slate-600">Shared with</h3>
                        <div class="mt-2 grid gap-2">
                            @foreach ($meeting->collaborators as $collaborator)
                                <div class="flex min-w-0 items-center gap-2">
                                    <span class="profile-avatar !h-7 !w-7 text-[10px]">{{ str($collaborator->name)->substr(0, 1)->upper() }}</span>
                                    <span class="truncate text-[10px] text-slate-600">{{ $collaborator->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>

            @if ($meeting->highlights->isNotEmpty())
                <section class="surface">
                    <div class="section-heading">
                        <div><h2>Highlights</h2><p>Notable moments from the meeting</p></div>
                    </div>
                    @foreach ($meeting->highlights as $highlight)
                        <div class="detail-list-item">
                            <span class="tag-chip">{{ $highlight->type }}</span>
                            <p class="mt-2">{{ $highlight->content }}</p>
                        </div>
                    @endforeach
                </section>
            @endif
        </aside>
    </div>

    @can('update', $meeting)
        @if ($meeting->status === 'failed')
            <form method="POST" action="{{ route('meetings.retry', $meeting) }}" class="mt-4">
                @csrf
                <button class="button button-primary" type="submit"><x-icon name="clock" class="h-4 w-4" /> Retry processing</button>
            </form>
        @endif
    @endcan

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const panel = document.getElementById('meeting-status-panel');
            if (!panel || !window.meetingRealtime) return;

            const badge = document.getElementById('meeting-status-badge');
            const message = document.getElementById('meeting-status-message');

            window.meetingRealtime.subscribe(panel.dataset.meetingId, (payload) => {
                if (!badge || !message) return;

                badge.textContent = payload.status;
                badge.className = 'status-pill ' + (
                    payload.status === 'completed'
                        ? 'bg-emerald-50 text-emerald-700'
                        : payload.status === 'failed'
                            ? 'bg-rose-50 text-rose-700'
                            : 'bg-amber-50 text-amber-700'
                );
                message.textContent = payload.message;

                if (payload.status === 'completed' || payload.status === 'failed') {
                    window.setTimeout(() => window.location.reload(), 1200);
                }
            });
        });
    </script>
</x-layouts.app>
