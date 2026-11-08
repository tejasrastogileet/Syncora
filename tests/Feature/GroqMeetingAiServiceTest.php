<?php

namespace Tests\Feature;

use App\Models\Meeting;
use App\Models\User;
use App\Services\MeetingAiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GroqMeetingAiServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_multipart_audio_to_groq_and_saves_structured_summary(): void
    {
        config([
            'services.groq.api_key' => 'test-groq-key',
            'services.groq.base_url' => 'https://api.groq.com/openai/v1',
            'services.groq.transcription_model' => 'whisper-large-v3-turbo',
            'services.groq.summary_model' => 'openai/gpt-oss-20b',
        ]);

        Storage::fake('local');
        Storage::disk('local')->put('meetings/demo.wav', 'fake audio');

        $owner = User::factory()->create();
        $meeting = Meeting::create([
            'user_id' => $owner->id,
            'title' => 'Product planning',
            'slug' => 'product-planning-test',
            'category' => 'Planning',
            'language' => 'en',
            'status' => 'queued',
            'media_disk' => 'local',
            'media_path' => 'meetings/demo.wav',
            'media_name' => 'demo.wav',
            'media_mime_type' => 'audio/wav',
            'media_size' => 10,
        ]);

        $summary = [
            'summary_markdown' => '## Summary'."\n".'The team agreed on the launch plan.',
            'key_points' => ['Prepare the launch plan.'],
            'speaker_overview' => [['speaker_name' => null, 'summary' => 'Discussed the launch plan.']],
            'action_items' => [[
                'assignee_name' => null,
                'assignee_email' => null,
                'description' => 'Prepare the launch plan.',
                'priority' => 'high',
                'status' => 'open',
                'due_date' => null,
            ]],
            'decisions' => [['speaker_name' => null, 'decision' => 'Proceed with launch planning.', 'rationale' => null]],
            'highlights' => [[
                'speaker_name' => null,
                'type' => 'decision',
                'score' => 1,
                'content' => 'Proceed with launch planning.',
            ]],
        ];

        $transcriptionRequestBody = '';

        Http::fake([
            'api.groq.com/openai/v1/audio/transcriptions' => function (Request $request) use (&$transcriptionRequestBody) {
                $transcriptionRequestBody = $request->body();

                return Http::response([
                    'text' => 'We agreed to prepare the launch plan.',
                    'segments' => [[
                        'start' => 0.5,
                        'end' => 2.4,
                        'text' => 'We agreed to prepare the launch plan.',
                    ]],
                ]);
            },
            'api.groq.com/openai/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => ['content' => json_encode($summary, JSON_THROW_ON_ERROR)],
                ]],
            ]),
        ]);

        app(MeetingAiService::class)->process($meeting);

        $this->assertDatabaseHas('meetings', [
            'id' => $meeting->id,
            'transcript_text' => 'We agreed to prepare the launch plan.',
            'summary_markdown' => $summary['summary_markdown'],
        ]);
        $this->assertDatabaseHas('meeting_transcript_segments', [
            'meeting_id' => $meeting->id,
            'speaker_name' => null,
            'start_second' => 0.5,
            'text' => 'We agreed to prepare the launch plan.',
        ]);
        $this->assertDatabaseHas('meeting_action_items', [
            'meeting_id' => $meeting->id,
            'description' => 'Prepare the launch plan.',
            'priority' => 'high',
        ]);

        Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/audio/transcriptions')
                && $request->isMultipart()
                && $request->hasFile('file', null, 'demo.wav'));
        $this->assertStringContainsString('whisper-large-v3-turbo', $transcriptionRequestBody);
        $this->assertStringContainsString('verbose_json', $transcriptionRequestBody);

        Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/chat/completions')
            && $request->isJson()
            && $request['model'] === 'openai/gpt-oss-20b'
            && $request['response_format']['type'] === 'json_schema'
            && $request['response_format']['json_schema']['strict'] === true);
    }
}
