@php
    $editing = isset($meeting);
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="field-label" for="meeting-title">Meeting title</label>
        <input id="meeting-title" type="text" name="title" value="{{ old('title', $meeting->title ?? '') }}" class="field-input" placeholder="e.g. Weekly product review" maxlength="255" required aria-describedby="{{ $errors->has('title') ? 'title-error' : '' }}">
        @error('title')
            <p id="title-error" class="field-error" role="alert">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="field-label" for="category">Category</label>
        <input id="category" type="text" name="category" value="{{ old('category', $meeting->category ?? '') }}" class="field-input" placeholder="Product, Sales, Team sync" maxlength="100">
        @error('category')
            <p class="field-error" role="alert">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="field-label" for="language">Transcript language</label>
        <input id="language" type="text" name="language" value="{{ old('language', $meeting->language ?? 'auto') }}" class="field-input" placeholder="auto or en" maxlength="30" aria-describedby="language-help">
        <p id="language-help" class="mt-1.5 text-[10px] text-slate-400">Use “auto” to let speech recognition detect the language.</p>
    </div>
    <div>
        <label class="field-label" for="scheduled_for">Scheduled for <span class="font-normal text-slate-400">(optional)</span></label>
        <input id="scheduled_for" type="datetime-local" name="scheduled_for" value="{{ old('scheduled_for', isset($meeting?->scheduled_for) ? $meeting->scheduled_for->format('Y-m-d\TH:i') : '') }}" class="field-input">
        @error('scheduled_for')
            <p class="field-error" role="alert">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="field-label" for="team_id">Share with a team</label>
        <select id="team_id" name="team_id" class="field-input">
            <option value="">Private meeting</option>
            @foreach ($teams as $team)
                <option value="{{ $team->id }}" @selected(old('team_id', $meeting->team_id ?? '') == $team->id)>{{ $team->name }}</option>
            @endforeach
        </select>
        @error('team_id')
            <p class="field-error" role="alert">{{ $message }}</p>
        @enderror
    </div>
    <div class="sm:col-span-2">
        <label class="field-label" for="tags">Tags <span class="font-normal text-slate-400">(optional)</span></label>
        <input id="tags" type="text" name="tags" value="{{ old('tags', isset($meeting) ? $meeting->tags->pluck('name')->implode(', ') : '') }}" class="field-input" placeholder="strategy, q3, customer feedback">
        <p class="mt-1.5 text-[10px] text-slate-400">Separate tags with commas.</p>
    </div>
    <div class="sm:col-span-2">
        <label class="field-label" for="collaborator_emails">Invite collaborators <span class="font-normal text-slate-400">(optional)</span></label>
        <input id="collaborator_emails" type="text" name="collaborator_emails" value="{{ old('collaborator_emails', isset($meeting) ? $meeting->collaborators->pluck('email')->implode(', ') : '') }}" class="field-input" placeholder="alex@company.com, taylor@company.com">
        <p class="mt-1.5 text-[10px] text-slate-400">Only registered users with matching email addresses will be added.</p>
    </div>
    @unless($editing)
        <div class="sm:col-span-2">
            <label class="field-label" for="media">Recording file</label>
            <input id="media" type="file" name="media" class="field-input file:mr-3 file:rounded-md file:border-0 file:bg-emerald-50 file:px-3 file:py-1.5 file:text-[11px] file:font-semibold file:text-emerald-800" accept=".mp3,.mp4,.mpeg,.mpga,.m4a,.wav,.webm" required aria-describedby="media-help {{ $errors->has('media') ? 'media-error' : '' }}">
            <p id="media-help" class="mt-1.5 text-[10px] text-slate-400">Supported: MP3, MP4, MPEG, MPGA, M4A, WAV, WEBM. Maximum 25 MB.</p>
            @error('media')
                <p id="media-error" class="field-error" role="alert">{{ $message }}</p>
            @enderror
        </div>
    @endunless
</div>
