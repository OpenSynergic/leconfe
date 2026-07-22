<?php

namespace Tests\Feature;

use App\Models\Conference;
use App\Models\Enums\SubmissionStatus;
use App\Models\Enums\UserRole;
use App\Models\Permission;
use App\Models\Role;
use App\Models\ScheduledConference;
use App\Models\Submission;
use App\Models\Topic;
use App\Models\Track;
use App\Models\User;
use App\Panel\ScheduledConference\Resources\SubmissionResource\Pages\ManageSubmissions;
use App\Providers\PanelProvider;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubmissionTopicFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_submission_list_can_filter_by_topic(): void
    {
        [$conference, $scheduledConference] = $this->makeConferenceContext();
        $editor = $this->makeEditor($conference, $scheduledConference);
        $track = Track::withoutGlobalScopes()->create([
            'scheduled_conference_id' => $scheduledConference->getKey(),
            'title' => 'Main Track',
            'abbreviation' => 'MT',
            'is_active' => true,
        ]);
        $selectedTopic = Topic::create(['name' => 'Artificial Intelligence']);
        $otherTopic = Topic::create(['name' => 'Security']);
        $selectedSubmission = $this->makeSubmission($conference, $scheduledConference, $track, $editor, 'AI Paper');
        $otherSubmission = $this->makeSubmission($conference, $scheduledConference, $track, $editor, 'Security Paper');
        $selectedSubmission->topics()->attach($selectedTopic);
        $otherSubmission->topics()->attach($otherTopic);
        $editorRole = $editor->roles()->firstOrFail();
        $selectedSubmission->participants()->create(['user_id' => $editor->getKey(), 'role_id' => $editorRole->getKey()]);
        $otherSubmission->participants()->create(['user_id' => $editor->getKey(), 'role_id' => $editorRole->getKey()]);

        Filament::setCurrentPanel(Filament::getPanel(PanelProvider::PANEL_SCHEDULED_CONFERENCE));

        $component = Livewire::actingAs($editor)
            ->test(ManageSubmissions::class);

        $component
            ->assertTableFilterExists('topic')
            ->filterTable('topic', [$selectedTopic->getKey()])
            ->assertCanSeeTableRecords([$selectedSubmission])
            ->assertCanNotSeeTableRecords([$otherSubmission])
            ->assertCountTableRecords(1);
    }

    /** @return array{Conference, ScheduledConference} */
    private function makeConferenceContext(): array
    {
        $conference = Conference::query()->create([
            'name' => 'Test Conference',
            'path' => 'test-conference',
        ]);
        $scheduledConference = ScheduledConference::withoutGlobalScopes()->create([
            'conference_id' => $conference->getKey(),
            'title' => 'Test Scheduled Conference',
            'path' => 'test-scheduled-conference',
            'date_start' => now()->toDateString(),
            'date_end' => now()->addDays(2)->toDateString(),
        ]);

        app()->setCurrentConferenceId($conference->getKey());
        app()->setCurrentScheduledConferenceId($scheduledConference->getKey());

        return [$conference, $scheduledConference];
    }

    private function makeEditor(Conference $conference, ScheduledConference $scheduledConference): User
    {
        $editor = User::query()->create([
            'given_name' => 'Editor',
            'family_name' => 'Tester',
            'email' => 'editor@example.test',
            'password' => 'password',
        ]);
        $role = Role::withoutGlobalScopes()
            ->where('name', UserRole::ScheduledConferenceEditor->value)
            ->where('conference_id', $conference->getKey())
            ->where('scheduled_conference_id', $scheduledConference->getKey())
            ->firstOrFail();

        Permission::query()->firstOrCreate([
            'name' => 'Submission:viewAny',
            'guard_name' => 'web',
        ]);
        $role->syncPermissions(['Submission:viewAny']);

        $editor->assignRole($role);

        return $editor;
    }

    private function makeSubmission(Conference $conference, ScheduledConference $scheduledConference, Track $track, User $author, string $title): Submission
    {
        $submission = Submission::withoutGlobalScopes()->forceCreate([
            'user_id' => $author->getKey(),
            'conference_id' => $conference->getKey(),
            'scheduled_conference_id' => $scheduledConference->getKey(),
            'track_id' => $track->getKey(),
            'status' => SubmissionStatus::Queued,
        ]);
        $submission->setMeta('title', $title);

        return $submission;
    }
}
