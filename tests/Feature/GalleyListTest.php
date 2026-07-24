<?php

namespace Tests\Feature;

use App\Actions\SubmissionGalleys\CreateSubmissionGalleyAction;
use App\Constants\SubmissionFileCategory;
use App\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Models\Conference;
use App\Models\ScheduledConference;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\SubmissionFileType;
use App\Models\Track;
use App\Models\User;
use App\Panel\ScheduledConference\Livewire\Submissions\Components\GalleyList;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Validator;
use InvalidArgumentException;
use Livewire\Livewire;
use Tests\TestCase;

class GalleyListTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_local_galley_from_an_uploaded_file(): void
    {
        Storage::fake('private-files');

        $context = $this->makeSubmissionContext();
        $fileType = SubmissionFileType::query()->create([
            'name' => 'PDF',
            'scheduled_conference_id' => $context['scheduledConference']->getKey(),
        ]);

        $this->actingAs($context['user']);

        $component = Livewire::test(GalleyList::class, ['submission' => $context['submission']])
            ->mountAction(TestAction::make('create')->table())
            ->fillForm([
                'label' => 'PDF',
                'is_remote_url' => false,
                'media' => [
                    'type' => $fileType->getKey(),
                    'files' => [
                        UploadedFile::fake()->create('camera-ready.pdf', 12, 'application/pdf'),
                    ],
                    'is_custom_name' => true,
                    'name' => 'custom-galley-name',
                ],
            ]);

        $component
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $galley = $context['submission']->galleys()->with('file.media')->sole();

        $this->assertNull($galley->remote_url);
        $this->assertNotNull($galley->submission_file_id);
        $this->assertSame('custom-galley-name.pdf', $galley->file->media->file_name);
        $this->assertSame($fileType->getKey(), $galley->file->submission_file_type_id);
        $this->assertSame(SubmissionFileCategory::GALLEY_FILES, $galley->file->category);
    }

    public function test_it_creates_a_remote_url_galley_without_an_upload_component(): void
    {
        app('validator')->resolver(
            fn ($translator, $data, $rules, $messages, $attributes) => new ValidatorWithFakeDnsRecords(
                $translator,
                $data,
                $rules,
                $messages,
                $attributes,
            ),
        );

        $context = $this->makeSubmissionContext();

        $this->actingAs($context['user']);

        Livewire::test(GalleyListWithoutFileUpload::class, ['submission' => $context['submission']])
            ->callAction(TestAction::make('create')->table(), [
                'label' => 'HTML',
                'is_remote_url' => true,
                'remote_url' => 'https://example.com/papers/galley',
            ])
            ->assertHasNoActionErrors();

        $galley = $context['submission']->galleys()->sole();

        $this->assertSame('https://example.com/papers/galley', $galley->remote_url);
        $this->assertNull($galley->submission_file_id);
        $this->assertSame(0, SubmissionFile::query()->count());
    }

    public function test_it_reports_a_validation_error_when_the_local_galley_upload_component_is_missing(): void
    {
        $context = $this->makeSubmissionContext();
        $fileType = SubmissionFileType::query()->create([
            'name' => 'PDF',
            'scheduled_conference_id' => $context['scheduledConference']->getKey(),
        ]);

        $this->actingAs($context['user']);

        Livewire::test(GalleyListWithoutFileUpload::class, ['submission' => $context['submission']])
            ->callAction(TestAction::make('create')->table(), [
                'label' => 'PDF',
                'is_remote_url' => false,
                'media' => [
                    'type' => $fileType->getKey(),
                ],
            ])
            ->assertHasActionErrors(['media.files']);

        $this->assertSame(0, $context['submission']->galleys()->count());
        $this->assertSame(0, SubmissionFile::query()->count());

        try {
            CreateSubmissionGalleyAction::run($context['submission'], [
                'label' => 'PDF',
                'is_remote_url' => false,
                'media' => [
                    'type' => $fileType->getKey(),
                ],
            ], null);

            $this->fail('The create action accepted a local galley without an upload component.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame(
                'A SpatieMediaLibraryFileUpload component is required when creating a local submission galley.',
                $exception->getMessage(),
            );
        }

        $this->assertSame(0, $context['submission']->galleys()->count());
    }

    /**
     * @return array{
     *     scheduledConference: ScheduledConference,
     *     submission: Submission,
     *     user: User
     * }
     */
    private function makeSubmissionContext(): array
    {
        $conference = Conference::query()->create([
            'name' => 'Conference '.uniqid(),
            'path' => 'conference-'.uniqid(),
        ]);

        $scheduledConference = ScheduledConference::withoutGlobalScopes()->create([
            'conference_id' => $conference->getKey(),
            'title' => 'Scheduled Conference '.uniqid(),
            'path' => 'scheduled-conference-'.uniqid(),
            'date_start' => now()->toDateString(),
            'date_end' => now()->addDays(2)->toDateString(),
        ]);

        app()->setCurrentConferenceId($conference->getKey());
        app()->setCurrentScheduledConferenceId($scheduledConference->getKey());

        $track = Track::withoutGlobalScopes()->create([
            'scheduled_conference_id' => $scheduledConference->getKey(),
            'title' => 'Track',
            'abbreviation' => 'TRK',
            'is_active' => true,
        ]);

        $user = User::query()->create([
            'given_name' => 'Author',
            'family_name' => 'Tester',
            'email' => 'author-'.uniqid().'@example.test',
            'password' => 'password123456',
        ]);

        $submission = Submission::withoutGlobalScopes()->forceCreate([
            'user_id' => $user->getKey(),
            'conference_id' => $conference->getKey(),
            'scheduled_conference_id' => $scheduledConference->getKey(),
            'track_id' => $track->getKey(),
        ]);

        return compact('scheduledConference', 'submission', 'user');
    }
}

class GalleyListWithoutFileUpload extends GalleyList
{
    public function getGalleyFormSchema(): array
    {
        return array_values(array_filter(
            parent::getGalleyFormSchema(),
            fn ($component): bool => ! $component instanceof SpatieMediaLibraryFileUpload,
        ));
    }
}

class ValidatorWithFakeDnsRecords extends Validator
{
    protected function getDnsRecords($hostname, $type): array
    {
        return [['ip' => '192.0.2.1']];
    }
}
