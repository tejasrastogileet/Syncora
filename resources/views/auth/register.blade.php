<x-layouts.app :title="'Create account'">
    <section class="auth-grid">
        <aside class="auth-aside">
            <div>
                <span class="marketing-kicker"><span></span> A clearer way to collaborate</span>
                <h1 class="mt-6">Your meetings, moving work forward.</h1>
                <p>Create a Syncora workspace to turn recordings into notes, decisions, and follow-through your team can find.</p>
            </div>
            <div class="auth-aside-note">
                <span class="metric-icon"><x-icon name="tasks" /></span>
                <span>One shared place for what happens next.</span>
            </div>
        </aside>

        <div class="auth-form-panel">
            <h2>Create your account</h2>
            <p class="auth-intro">Start with your details. You can invite teammates when sharing a meeting.</p>

            <form method="POST" action="{{ route('register.store') }}" class="auth-form" data-loading-form>
                @csrf
                <div>
                    <label class="field-label" for="name">Full name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" class="field-input" placeholder="Your name" autocomplete="name" required autofocus aria-describedby="{{ $errors->has('name') ? 'name-error' : '' }}">
                    @error('name')
                        <p id="name-error" class="field-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="field-label" for="email">Work email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="field-input" placeholder="you@company.com" autocomplete="email" required aria-describedby="{{ $errors->has('email') ? 'email-error' : '' }}">
                    @error('email')
                        <p id="email-error" class="field-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="field-label" for="job_title">Job title <span class="font-normal text-slate-400">(optional)</span></label>
                    <input id="job_title" type="text" name="job_title" value="{{ old('job_title') }}" class="field-input" placeholder="e.g. Product Manager" autocomplete="organization-title" aria-describedby="{{ $errors->has('job_title') ? 'job-title-error' : '' }}">
                    @error('job_title')
                        <p id="job-title-error" class="field-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="field-label" for="password">Password</label>
                        <div class="password-field">
                            <input id="password" type="password" name="password" class="field-input" placeholder="Create password" autocomplete="new-password" required aria-describedby="{{ $errors->has('password') ? 'password-error' : '' }}">
                            <button class="password-toggle" type="button" data-password-toggle aria-controls="password" aria-label="Show password">Show</button>
                        </div>
                        @error('password')
                            <p id="password-error" class="field-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="field-label" for="password_confirmation">Confirm password</label>
                        <div class="password-field">
                            <input id="password_confirmation" type="password" name="password_confirmation" class="field-input" placeholder="Repeat password" autocomplete="new-password" required>
                            <button class="password-toggle" type="button" data-password-toggle aria-controls="password_confirmation" aria-label="Show password">Show</button>
                        </div>
                    </div>
                </div>
                <button class="button button-primary auth-submit" type="submit" data-loading-label="Creating account...">Create account</button>
            </form>

            <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
        </div>
    </section>
</x-layouts.app>
