<?php

namespace App\Panel\Resources\SubmissionResource\Pages;

use App\Actions\Submissions\SubmissionCreateAction;
use App\Panel\Resources\SubmissionResource;
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

    protected static string $view = 'panel.resources.submission-resource.pages.create-submission';

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
            'disable_submission' => setting('disable_submission'),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('meta.title')
                ->required(),
            // CheckboxList::make('topics')
            //     ->required(),
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

        return redirect()->to(SubmissionResource::getUrl('view', [$submission]));
    }

    // protected function getBreadcrumbs(): array
    // {
    //     return [];
    // }
}
