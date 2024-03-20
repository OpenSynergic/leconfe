<?php

namespace App\Panel\Conference\Resources\SubmissionResource\Pages;

use App\Actions\Submissions\SubmissionCreateAction;
use App\Panel\Conference\Livewire\Workflows\Classes\StageManager;
use App\Panel\Conference\Resources\SubmissionResource;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;

class CreateSubmission extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $title = '';

    protected static string $resource = SubmissionResource::class;

    protected static string $view = 'panel.conference.resources.submission-resource.pages.create-submission';

    public $data;

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function mount(): void
    {
        $this->form->fill([]);
    }

    protected function getViewData(): array
    {
        return [
            'isOpen' => StageManager::callForAbstract()->isStageOpen(),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('meta.title')
                ->required(),
            Section::make('Privacy Consent')
                ->schema([
                    Checkbox::make('privacy_consent')
                        ->inline()
                        ->required()
                        ->label('Yes, I agree to have my data collected and stored according to the privacy statement.'),
                ]),
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();

        $submission = SubmissionCreateAction::run($data);

        return redirect()->to(SubmissionResource::getUrl('view', [$submission->id]));
    }
}
