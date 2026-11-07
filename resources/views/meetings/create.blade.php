<x-layouts.app
    :title="'New meeting'"
    :heading="'New meeting'"
    :subheading="'Add a recording and the context your team needs.'"
>
    <div class="mx-auto max-w-3xl">
        <section class="surface p-5 sm:p-7">
            <div class="mb-6 border-b border-slate-100 pb-5">
                <span class="metric-icon"><x-icon name="upload" /></span>
                <h2 class="mt-4 text-base font-semibold tracking-tight text-slate-800">Meeting details</h2>
                <p class="mt-1 text-xs leading-5 text-slate-500">Your file will be stored securely and queued for transcription and AI notes.</p>
            </div>

            <form method="POST" action="{{ route('meetings.store') }}" enctype="multipart/form-data" class="grid gap-6" data-loading-form>
                @csrf
                @include('meetings._form', ['teams' => $teams])
                <div class="form-actions">
                    <a href="{{ route('meetings.index') }}" class="button button-secondary">Cancel</a>
                    <button class="button button-primary" type="submit" data-loading-label="Uploading..."><x-icon name="upload" class="h-4 w-4" /> Upload meeting</button>
                </div>
            </form>
        </section>
    </div>
</x-layouts.app>
