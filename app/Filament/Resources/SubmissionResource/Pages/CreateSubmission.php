<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use App\Actions\Submissions\CreateSubmissionAction;
use App\Filament\Resources\SubmissionResource;
use App\Models\Submission;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Actions\Action;
use Filament\Pages\Concerns\HasFormActions;
use Filament\Resources\Pages\Page;
use FilamentTiptapEditor\TiptapEditor;

class CreateSubmission extends Page implements HasForms
{
    use InteractsWithForms, HasFormActions;

    protected static ?string $title = '';

    protected static string $resource = SubmissionResource::class;

    protected static string $view = 'filament.resources.submission-resource.pages.create-submission';

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
            'disable_submission' => setting('disable_submission')
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('meta.title')
                ->required(),
            CheckboxList::make('topics')
                
                ->required(),
            Section::make('Privacy Consent')
                ->schema([
                    Checkbox::make('privacy_consent')
                        ->inline()
                        ->required()
                        ->label('Yes, I agree to have my data collected and stored according to the privacy statement.')
                ]),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Begin Submission')
                ->extraAttributes([
                    'class' => 'w-full'
                ])
                ->submit('create')
                ->keyBindings(['mod+s'])
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();

        $submission = CreateSubmissionAction::run($data);

        return redirect()->to(SubmissionResource::getUrl('view', $submission));
    }


    // protected function getBreadcrumbs(): array
    // {
    //     return [];
    // }
}
