<?php

namespace Tests\Feature;

use App\Frontend\ScheduledConference\Pages\Home as ScheduledConferenceHome;
use App\Models\Conference;
use App\Models\ScheduledConference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduledConferenceTimezoneTest extends TestCase
{
    use RefreshDatabase;

    public function test_scheduled_conference_timezone_defaults_and_attribute(): void
    {
        $conference = Conference::factory()->create();
        $scheduledConference = ScheduledConference::factory()->create([
            'conference_id' => $conference->id,
        ]);

        $this->assertEquals(config('app.timezone', 'UTC'), $scheduledConference->getTimezone());

        $scheduledConference->setMeta('timezone', 'Asia/Jakarta');
        $this->assertEquals('Asia/Jakarta', $scheduledConference->getTimezone());

        $label = $scheduledConference->timezone_label;
        $this->assertStringContainsString('Asia/Jakarta', $label);
        $this->assertStringContainsString('UTC+07:00', $label);
    }

    public function test_middleware_sets_app_timezone_on_request(): void
    {
        $conference = Conference::factory()->create(['path' => 'test-conf']);
        $scheduledConference = ScheduledConference::factory()->create([
            'conference_id' => $conference->id,
            'path' => '2026',
            'is_published' => true,
        ]);
        $scheduledConference->setMeta('timezone', 'Asia/Tokyo');

        app()->setCurrentConferenceId($conference->getKey());
        app()->setCurrentScheduledConferenceId($scheduledConference->getKey());

        $this->withoutVite()
            ->get(route(ScheduledConferenceHome::getRouteName('scheduledConference'), [
                'conference' => $conference->path,
                'serie' => $scheduledConference->path,
            ]))
            ->assertOk();

        $this->assertEquals('Asia/Tokyo', config('app.timezone'));
        $this->assertEquals('Asia/Tokyo', date_default_timezone_get());
    }
}
