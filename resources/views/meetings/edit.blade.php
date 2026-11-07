<x-layouts.app
    :title="'Edit meeting'"
    :heading="'Edit meeting'"
    :subheading="'Update the details and sharing for this recording.'"
>
    <div class="mx-auto max-w-3xl">
        <section class="surface p-5 sm:p-7">
            <div class="mb-6 border-b border-slate-100 pb-5">
                <span class="metric-icon"><x-icon name="settings" /></span>
                <h2 class="mt-4 text-base font-semibold tracking-tight text-slate-800">{{ $meeting->title }}</h2>
                <p class="mt-1 text-xs leading-5 text-slate-500">Update meeting metadata, tags, and registered collaborators. The recording itself cannot be replaced here.</p>
            </div>

            <form method="POST" action="{{ route('meetings.update', $meeting) }}" class="grid gap-6" data-loading-form>
                @csrf
                @method('PUT')
                @include('meetings._form', ['teams' => $teams, 'meeting' => $meeting])
                <div class="form-actions">
                    <a href="{{ route('meetings.show', $meeting) }}" class="button button-secondary">Cancel</a>
                    <button class="button button-primary" type="submit" data-loading-label="Saving...">Save changes</button>
                </div>
            </form>
        </section>
    </div>
</x-layouts.app>
