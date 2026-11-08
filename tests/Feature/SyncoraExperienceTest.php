<?php

namespace Tests\Feature;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SyncoraExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_branding_and_authentication_pages_render(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Turn conversations')
            ->assertSee('syncora');

        $this->get('/login')
            ->assertOk()
            ->assertSee('name="email"', false)
            ->assertSee('data-password-toggle', false);

        $this->get('/register')
            ->assertOk()
            ->assertSee('name="password_confirmation"', false)
            ->assertSee('data-password-toggle', false);
    }

    public function test_authenticated_workspace_and_meeting_experience_render_real_data(): void
    {
        $owner = User::factory()->create();
        $this->actingAs($owner);
        Storage::fake('local');
        Storage::disk('local')->put('meetings/quarterly-planning.mp3', 'audio contents');

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Overview')
            ->assertSee('Recent meetings');

        $this->get('/meetings')
            ->assertOk()
            ->assertSee('Search titles, categories, or transcript');

        $this->get('/meetings/create')
            ->assertOk()
            ->assertSee('Meeting details');

        $meeting = Meeting::create([
            'user_id' => $owner->id,
            'title' => 'Quarterly planning',
            'slug' => 'quarterly-planning-syncora-test',
            'category' => 'Planning',
            'language' => 'en',
            'status' => 'queued',
            'media_disk' => 'local',
            'media_path' => 'meetings/quarterly-planning.mp3',
            'media_name' => 'quarterly-planning.mp3',
            'media_mime_type' => 'audio/mpeg',
            'media_size' => 1024,
        ]);

        $this->get(route('meetings.show', $meeting))
            ->assertOk()
            ->assertSee('Recording')
            ->assertSee('AI summary')
            ->assertSee('Transcript')
            ->assertSee('Action items')
            ->assertSee(route('meetings.media', $meeting), false);

        $this->get(route('meetings.media', $meeting))
            ->assertOk()
            ->assertHeader('Content-Type', 'audio/mpeg')
            ->assertStreamedContent('audio contents');

        $this->get(route('meetings.edit', $meeting))
            ->assertOk()
            ->assertSee('Update meeting metadata');

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->get(route('admin.analytics'))
            ->assertOk()
            ->assertSee('Meetings by category')
            ->assertSee('Recent processing failures');
    }

    public function test_users_cannot_play_meetings_they_cannot_view(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $meeting = Meeting::create([
            'user_id' => $owner->id,
            'title' => 'Private meeting',
            'slug' => 'private-meeting-syncora-test',
            'status' => 'queued',
            'media_disk' => 'local',
            'media_path' => 'meetings/private.mp3',
            'media_name' => 'private.mp3',
            'media_mime_type' => 'audio/mpeg',
            'media_size' => 12,
        ]);
        Storage::fake('local');
        Storage::disk('local')->put('meetings/private.mp3', 'private audio');

        $this->actingAs($otherUser)
            ->get(route('meetings.media', $meeting))
            ->assertForbidden();
    }
}
