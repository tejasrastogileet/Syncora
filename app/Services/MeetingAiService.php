<?php

namespace App\Services;

use App\Models\Meeting;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class MeetingAiService
{
    public function process(Meeting $meeting): array
    {
        $transcription = $this->transcribe($meeting);
        $summary = $this->summarize($meeting, $transcription['text']);

        $meeting->update([
            'transcript_text' => $transcription['text'],
            'summary_markdown' => $summary['summary_markdown'],
            'key_points' => $summary['key_points'],
            'speaker_overview' => $summary['speaker_overview'],
            'metadata' => array_merge($meeting->metadata ?? [], [
                'transcription_model' => config('services.groq.transcription_model'),
                'summary_model' => config('services.groq.summary_model'),
            ]),
        ]);

        $meeting->transcriptSegments()->delete();
        $meeting->actionItems()->delete();
        $meeting->decisions()->delete();
        $meeting->highlights()->delete();

        foreach ($transcription['segments'] as $segment) {
            $meeting->transcriptSegments()->create($segment);
        }

        foreach ($summary['action_items'] as $item) {
            $meeting->actionItems()->create($item);
        }

        foreach ($summary['decisions'] as $decision) {
            $meeting->decisions()->create($decision);
        }

        foreach ($summary['highlights'] as $highlight) {
            $meeting->highlights()->create($highlight);
        }

        return [
            'segments' => $transcription['segments'],
            'action_items' => $summary['action_items'],
            'decisions' => $summary['decisions'],
        ];
    }

    protected function transcribe(Meeting $meeting): array
    {
        if (! config('services.groq.api_key')) {
            return $this->fallbackTranscription($meeting);
        }

        $absolutePath = Storage::disk($meeting->media_disk)->path($meeting->media_path);
        $audioFile = fopen($absolutePath, 'rb');

        if ($audioFile === false) {
            throw new RuntimeException("Unable to open meeting media file: {$meeting->media_path}");
        }

        try {
            $transcriptionRequest = [
                'model' => config('services.groq.transcription_model'),
                'response_format' => 'verbose_json',
            ];

            if ($meeting->language !== 'auto') {
                $transcriptionRequest['language'] = $meeting->language;
            }

            $response = $this->client()
                ->attach('file', $audioFile, $meeting->media_name)
                ->post('audio/transcriptions', $transcriptionRequest)
                ->throw()
                ->json();
        } finally {
            fclose($audioFile);
        }

        if (! is_array($response)) {
            throw new RuntimeException('Groq transcription returned an invalid response.');
        }

        $segments = collect($response['segments'] ?? [])
            ->filter(fn ($segment) => is_array($segment))
            ->map(fn (array $segment) => [
                'speaker_name' => null,
                'speaker_confidence' => null,
                'start_second' => $segment['start'] ?? null,
                'end_second' => $segment['end'] ?? null,
                'text' => is_string($segment['text'] ?? null) ? trim($segment['text']) : '',
                'metadata' => $segment,
            ])
            ->filter(fn (array $segment) => $segment['text'] !== '')
            ->values()
            ->all();

        $text = is_string($response['text'] ?? null) ? trim($response['text']) : '';

        if ($segments === []) {
            if ($text === '') {
                throw new RuntimeException('Groq transcription response did not contain transcript text.');
            }

            $segments = [[
                'speaker_name' => null,
                'speaker_confidence' => null,
                'start_second' => 0,
                'end_second' => null,
                'text' => $text,
                'metadata' => [],
            ]];
        }

        return [
            'text' => $text !== '' ? $text : collect($segments)->pluck('text')->implode(' '),
            'segments' => $segments,
        ];
    }

    protected function summarize(Meeting $meeting, string $transcript): array
    {
        if (! config('services.groq.api_key')) {
            return $this->fallbackSummary($meeting, $transcript);
        }

        $response = $this->client()
            ->post('chat/completions', [
                'model' => config('services.groq.summary_model'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Summarize meeting transcripts accurately. Do not invent speaker identities; use null when the transcript does not identify a speaker.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Summarize the following meeting transcript. Extract key points, concise speaker highlights, explicit decisions, and actionable tasks.\n\nMeeting title: {$meeting->title}\nCategory: {$meeting->category}\nTranscript:\n{$transcript}",
                    ],
                ],
                'response_format' => [
                    'type' => 'json_schema',
                    'json_schema' => [
                        'name' => 'meeting_summary',
                        'strict' => true,
                        'schema' => [
                            'type' => 'object',
                            'additionalProperties' => false,
                            'properties' => [
                                'summary_markdown' => ['type' => 'string'],
                                'key_points' => ['type' => 'array', 'items' => ['type' => 'string']],
                                'speaker_overview' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'additionalProperties' => false,
                                        'properties' => [
                                            'speaker_name' => ['type' => ['string', 'null']],
                                            'summary' => ['type' => 'string'],
                                        ],
                                        'required' => ['speaker_name', 'summary'],
                                    ],
                                ],
                                'action_items' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'additionalProperties' => false,
                                        'properties' => [
                                            'assignee_name' => ['type' => ['string', 'null']],
                                            'assignee_email' => ['type' => ['string', 'null']],
                                            'description' => ['type' => 'string'],
                                            'priority' => ['type' => 'string'],
                                            'status' => ['type' => 'string'],
                                            'due_date' => ['type' => ['string', 'null']],
                                        ],
                                        'required' => ['assignee_name', 'assignee_email', 'description', 'priority', 'status', 'due_date'],
                                    ],
                                ],
                                'decisions' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'additionalProperties' => false,
                                        'properties' => [
                                            'speaker_name' => ['type' => ['string', 'null']],
                                            'decision' => ['type' => 'string'],
                                            'rationale' => ['type' => ['string', 'null']],
                                        ],
                                        'required' => ['speaker_name', 'decision', 'rationale'],
                                    ],
                                ],
                                'highlights' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'additionalProperties' => false,
                                        'properties' => [
                                            'speaker_name' => ['type' => ['string', 'null']],
                                            'type' => ['type' => 'string'],
                                            'score' => ['type' => ['number', 'null']],
                                            'content' => ['type' => 'string'],
                                        ],
                                        'required' => ['speaker_name', 'type', 'score', 'content'],
                                    ],
                                ],
                            ],
                            'required' => ['summary_markdown', 'key_points', 'speaker_overview', 'action_items', 'decisions', 'highlights'],
                        ],
                    ],
                ],
            ])
            ->throw()
            ->json();

        $json = $this->extractStructuredResponse($response);

        return [
            'summary_markdown' => $json['summary_markdown'],
            'key_points' => $json['key_points'],
            'speaker_overview' => $json['speaker_overview'],
            'action_items' => $json['action_items'],
            'decisions' => $json['decisions'],
            'highlights' => $json['highlights'],
        ];
    }

    protected function extractStructuredResponse(array $response): array
    {
        $text = data_get($response, 'choices.0.message.content');

        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('Unable to parse Groq structured response.');
        }

        $decoded = json_decode($text, true);

        if (! is_array($decoded)) {
            throw new RuntimeException('Invalid JSON returned by Groq chat completion endpoint.');
        }

        return $decoded;
    }

    protected function fallbackTranscription(Meeting $meeting): array
    {
        $text = "Groq API key is not configured. Upload received for {$meeting->title}. Add GROQ_API_KEY to enable transcription and AI summarization.";

        return [
            'text' => $text,
            'segments' => [[
                'speaker_name' => null,
                'speaker_confidence' => null,
                'start_second' => 0,
                'end_second' => null,
                'text' => $text,
                'metadata' => ['fallback' => true],
            ]],
        ];
    }

    protected function fallbackSummary(Meeting $meeting, string $transcript): array
    {
        return [
            'summary_markdown' => "## Summary\n{$transcript}",
            'key_points' => [
                'Upload stored successfully.',
                'Configure GROQ_API_KEY to enable live transcription and structured meeting intelligence.',
            ],
            'speaker_overview' => [[
                'speaker_name' => null,
                'summary' => 'Placeholder summary generated because Groq credentials are missing.',
            ]],
            'action_items' => [[
                'assignee_name' => $meeting->owner->name,
                'assignee_email' => $meeting->owner->email,
                'description' => 'Add Groq API credentials and reprocess this meeting.',
                'priority' => 'high',
                'status' => 'open',
                'due_date' => now()->addDay()->toDateString(),
            ]],
            'decisions' => [[
                'speaker_name' => null,
                'decision' => 'Meeting processing is waiting for Groq configuration.',
                'rationale' => 'The application requires a Groq API key for transcription and summarization.',
            ]],
            'highlights' => [[
                'speaker_name' => null,
                'type' => 'configuration',
                'score' => 1,
                'content' => 'Meeting uploaded and queued, but AI enrichment is currently using a fallback response.',
            ]],
        ];
    }

    protected function client(): PendingRequest
    {
        return Http::baseUrl(rtrim(config('services.groq.base_url'), '/').'/')
            ->withToken(config('services.groq.api_key'))
            ->acceptJson()
            ->timeout(600);
    }
}
