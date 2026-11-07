<x-layouts.app :title="'Sign in'">
    <section class="auth-grid">
        <aside class="auth-aside">
            <div>
                <span class="marketing-kicker"><span></span> Meeting intelligence, made clear</span>
                <h1 class="mt-6">Make every conversation count.</h1>
                <p>Bring your meetings, decisions, and next steps into one calm, searchable workspace.</p>
            </div>
            <div class="auth-aside-note">
                <span class="metric-icon"><x-icon name="notes" /></span>
                <span>Turn conversations into clarity.</span>
            </div>
        </aside>

        <div class="auth-form-panel">
            <h2>Welcome back</h2>
            <p class="auth-intro">Sign in to pick up where your team left off.</p>

            <form method="POST" action="{{ route('login.store') }}" class="auth-form" data-loading-form>
                @csrf
                <div>
                    <label class="field-label" for="email">Work email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="field-input" placeholder="you@company.com" autocomplete="email" required autofocus aria-describedby="{{ $errors->has('email') ? 'email-error' : '' }}">
                    @error('email')
                        <p id="email-error" class="field-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <div class="mb-1.5 flex items-center justify-between gap-3">
                        <label class="field-label mb-0" for="password">Password</label>
                    </div>
                    <div class="password-field">
                        <input id="password" type="password" name="password" class="field-input" placeholder="Enter your password" autocomplete="current-password" required aria-describedby="{{ $errors->has('password') ? 'password-error' : '' }}">
                        <button class="password-toggle" type="button" data-password-toggle aria-controls="password" aria-label="Show password">Show</button>
                    </div>
                    @error('password')
                        <p id="password-error" class="field-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>
                <label class="flex items-center gap-2 text-[11px] text-slate-600">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember')) class="h-4 w-4 rounded border-slate-300 text-teal-700 focus:ring-teal-700/20">
                    Keep me signed in
                </label>
                <button class="button button-primary auth-submit" type="submit" data-loading-label="Signing in...">Sign in</button>
            </form>

            <p class="auth-switch">New to Syncora? <a href="{{ route('register') }}">Create your workspace</a></p>
        </div>
    </section>
</x-layouts.app>
