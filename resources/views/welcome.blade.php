<x-layouts.app :title="'Syncora — Meeting intelligence'" >
    <div class="marketing-shell">
        <header class="marketing-nav">
            <a href="{{ route('welcome') }}" class="brand-lockup" aria-label="Syncora home">
                <x-brand-mark class="brand-mark" />
                <span class="brand-copy">
                    <span class="brand-name">syncora</span>
                    <span class="brand-tagline">Turn conversations into clarity.</span>
                </span>
            </a>
            <nav class="marketing-nav-actions" aria-label="Main navigation">
                <a href="#how-it-works">How it works</a>
                @auth
                    <a href="{{ route('dashboard') }}">Go to workspace</a>
                @else
                    <a href="{{ route('login') }}">Sign in</a>
                    <a href="{{ route('register') }}" class="button button-primary">Get started</a>
                @endauth
            </nav>
        </header>

        <section class="marketing-hero">
            <div>
                <span class="marketing-kicker"><span></span> Meeting intelligence for modern teams</span>
                <h1>Turn conversations <span>into clarity.</span></h1>
                <p class="marketing-hero-copy">Syncora transforms meeting recordings into organized transcripts, thoughtful summaries, clear decisions, and actionable next steps—all in one searchable workspace.</p>
                <div class="marketing-cta-row">
                    @auth
                        <a href="{{ route('dashboard') }}" class="button button-primary">Open your workspace <x-icon name="arrow" class="h-4 w-4" /></a>
                    @else
                        <a href="{{ route('register') }}" class="button button-primary">Create your workspace <x-icon name="arrow" class="h-4 w-4" /></a>
                        <a href="{{ route('login') }}" class="button button-secondary">Sign in</a>
                    @endauth
                </div>
                <p class="marketing-proof">Your recordings stay in your workspace. Review notes and follow-ups when you’re ready.</p>
            </div>

            <div class="product-preview" aria-label="Illustration of a meeting summary">
                <div class="preview-topbar">
                    <div class="preview-dots"><span></span><span></span><span></span></div>
                    <span class="preview-label">SYNCORA · MEETING NOTES</span>
                    <x-icon name="notes" class="h-4 w-4 text-slate-400" />
                </div>
                <div class="preview-content">
                    <div>
                        <p class="preview-meeting-title">Product direction sync</p>
                        <p class="preview-meeting-meta">Summary · Decisions · Next steps</p>
                    </div>
                    <div class="preview-summary">
                        <p class="preview-section-title">Meeting summary</p>
                        <div class="preview-line"></div>
                        <div class="preview-line"></div>
                        <div class="preview-line short"></div>
                    </div>
                    <div class="preview-summary">
                        <p class="preview-section-title">Action items</p>
                        <div class="preview-task"><span class="preview-check"></span><span>Share the revised launch timeline</span></div>
                        <div class="preview-task"><span class="preview-check"></span><span>Review customer feedback themes</span></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="how-it-works" class="marketing-features">
            <h2>From the conversation to the next step</h2>
            <div class="feature-grid">
                <article class="feature-item">
                    <span class="metric-icon"><x-icon name="upload" /></span>
                    <h3>Bring your recording</h3>
                    <p>Upload an audio or video meeting recording into your private workspace.</p>
                </article>
                <article class="feature-item">
                    <span class="metric-icon"><x-icon name="notes" /></span>
                    <h3>Get organized notes</h3>
                    <p>Review a searchable transcript, meeting summary, key points, and decisions.</p>
                </article>
                <article class="feature-item">
                    <span class="metric-icon"><x-icon name="tasks" /></span>
                    <h3>Keep work moving</h3>
                    <p>Find extracted follow-ups, share meeting notes with your team, and export a copy.</p>
                </article>
            </div>
        </section>
    </div>
</x-layouts.app>
