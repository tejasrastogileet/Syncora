# Syncora

**Turn conversations into clarity.**

Syncora is a Laravel 12 meeting intelligence workspace. Upload audio or video recordings to create timestamped transcripts, AI-generated summaries, discussion points, decisions, action items, and highlights.

## Project

- GitHub: [tejasrastogileet/Syncora](https://github.com/tejasrastogileet/Syncora)
- Author: Tejas Rastogi

## Stack

- Laravel 12 and PHP 8.2+
- Blade, Tailwind CSS 4, Vite, and Livewire
- MySQL (SQLite is also supported by Laravel configuration)
- Groq API for Whisper speech-to-text and structured meeting summaries
- Laravel Reverb / Echo for real-time processing updates
- DOMPDF and PHPWord for meeting exports

## Features

- Account registration, sign-in, and role-aware access
- Private audio/video uploads with queued processing
- Timestamped transcripts and structured AI summaries
- Key discussion points, decisions, action items, and highlights
- Meeting search and filtering
- Team and registered-user meeting sharing
- PDF and DOCX exports
- Database notifications, real-time status updates, and audit logs
- Admin analytics for platform totals, categories, and processing failures

Workspace switching, a dedicated action-item management page, and account settings do not currently have backend functionality; the navigation labels are shown as disabled placeholders.

## Setup

Install PHP 8.2+, Composer, Node.js, and a configured MySQL server. Enable the required PHP extensions for your database driver (for MySQL, PDO MySQL; for SQLite, PDO SQLite).

```bash
composer install
npm install
cp .env.example .env
```

Configure `.env` for your local database and Groq account:

```env
APP_NAME=Syncora
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=syncora
DB_USERNAME=root
DB_PASSWORD=

GROQ_API_KEY=your_groq_api_key
GROQ_BASE_URL=https://api.groq.com/openai/v1
GROQ_TRANSCRIPTION_MODEL=whisper-large-v3-turbo
GROQ_SUMMARY_MODEL=openai/gpt-oss-20b

BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=database
```

Keep API keys in the server-side `.env`; do not commit them or expose them through Vite variables.

After saving your environment settings, finish initialization and compile the frontend:

```bash
php artisan key:generate
php artisan migrate
npm run build
```

## Run locally

Start each process in a separate terminal from the project directory:

```bash
php artisan serve
php artisan queue:work
npm run dev
php artisan reverb:start
```

The web application is available at `http://127.0.0.1:8000`. Reverb is optional for live status broadcasts; the meeting detail page also refreshes processing status using Livewire.

## AI processing notes

- `whisper-large-v3-turbo` transcribes uploaded audio and returns timestamped segments. Groq Whisper does not identify individual speakers; speaker attribution is left unknown rather than inferred.
- `openai/gpt-oss-20b` returns structured meeting summaries, decisions, and action items through Groq's chat-completions API.
- Groq's free-tier transcription size limits can change; the application currently limits uploads to 25 MB.
- Without `GROQ_API_KEY`, the queue pipeline completes using clearly labeled fallback content so the app can be explored without live AI processing.

See Groq's [models](https://console.groq.com/docs/models), [speech-to-text](https://console.groq.com/docs/speech-to-text), and [structured-output](https://console.groq.com/docs/structured-outputs) documentation for current provider details and limits.
