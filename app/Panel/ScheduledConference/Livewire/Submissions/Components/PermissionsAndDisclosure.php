<?php

namespace App\Panel\ScheduledConference\Livewire\Submissions\Components;

use Livewire\Component;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Actions\Action;
use Throwable;
use App\Actions\Submissions\SubmissionUpdateAction;
use App\Facades\License;
use App\Models\Submission;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

class PermissionsAndDisclosure extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Submission $submission;

    public ?array $formData = [];

    public bool $overrideCopyrightHolder = false;

    public bool $overrideCopyrightYear = false;

    public bool $overrideLicenseUrl = false;

    public function render()
    {
        return view('panel.scheduledConference.livewire.submissions.components.permission-and-disclosure');
    }

    public function mount(Submission $submission)
    {
        $this->form->fill([
            'meta' => $submission->getAllMeta()->toArray(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model($this->submission)
            ->disabled(function (): bool {
                return ! auth()->user()->can('editing', $this->submission);
            })
            ->schema([
                TextInput::make('meta.copyright_holder')
                    ->label(__('general.submission_copyright_holder'))
                    ->helperText(__('general.submission_copyright_holder_helper', [
                        'copyrightHolder' => app()->getCurrentConference()->getCopyrightHolderForSubmission($this->submission),
                    ]))
                    ->maxWidth(Width::Large)
                    ->disabled(fn () => ! $this->submission->getMeta('copyright_holder') && ! $this->overrideCopyrightHolder)
                    ->suffixActions([
                        Action::make('overridde')
                            ->label(__('general.override'))
                            ->link()
                            ->color('primary')
                            ->hidden(fn () => $this->submission->getMeta('copyright_holder') || $schema->isDisabled())
                            ->action(fn () => $this->overrideCopyrightHolder = true),
                    ]),
                TextInput::make('meta.copyright_year')
                    ->type('number')
                    ->minLength(0)
                    ->label(__('general.submission_copyright_year'))
                    ->helperText(__('general.submission_copyright_year_helper'))
                    ->maxWidth(Width::Large)
                    ->disabled(fn () => ! $this->submission->getMeta('copyright_year') && ! $this->overrideCopyrightYear)
                    ->suffixActions([
                        Action::make('overridde')
                            ->label(__('general.override'))
                            ->link()
                            ->color('primary')
                            ->hidden(fn () => $this->submission->getMeta('copyright_year') || $schema->isDisabled())
                            ->action(fn () => $this->overrideCopyrightYear = true),
                    ]),
                TextInput::make('meta.license_url')
                    ->label(__('general.submission_license_url'))
                    ->helperText(function () {
                        $licenseOptions = License::getCCLicenseOptions();
                        $licenseUrl = app()->getCurrentConference()->getLicenseUrl();
                        if (array_key_exists($licenseUrl, $licenseOptions)) {
                            $licenseName = $licenseOptions[$licenseUrl];
                        } else {
                            $licenseName = $licenseUrl;
                        }

                        return new HtmlString(__('general.submission_license_url_helper', [
                            'licenseUrl' => $licenseUrl,
                            'licenseName' => $licenseName,
                        ]));
                    })
                    ->maxWidth(Width::Large)
                    ->disabled(fn () => ! $this->submission->getMeta('license_url') && ! $this->overrideLicenseUrl)
                    ->suffixActions([
                        Action::make('overridde')
                            ->label(__('general.override'))
                            ->link()
                            ->color('primary')
                            ->hidden(fn () => $this->submission->getMeta('license_url') || $schema->isDisabled())
                            ->action(fn () => $this->overrideLicenseUrl = true),
                    ]),
            ])
            ->statePath('formData');
    }

    public function submit()
    {
        $data = $this->form->getState();
        try {
            $submission = SubmissionUpdateAction::run(
                $data,
                $this->submission
            );

            $this->form->model($submission)->saveRelationships();

            Notification::make()
                ->success()
                ->title(__('general.saved'))
                ->send();
        } catch (Throwable $th) {
            Notification::make()
                ->danger()
                ->title(__('general.error'))
                ->body(__('general.there_was_error_please_contact_administrator'))
                ->send();

            Log::error($th);
        }
    }
}
