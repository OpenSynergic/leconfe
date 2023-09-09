<?php

namespace App\Panel\Resources\SubmissionResource\Pages;

use App\Actions\Submissions\SubmissionCreateAction;
use App\Infolists\Components\BladeEntry;
use App\Models\Author;
use App\Panel\Resources\SubmissionResource;
use Closure;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\Page;

class CreateSubmission extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $title = 'Create a submission';

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
            Wizard::make([
                Wizard\Step::make('Start')
                    ->schema([
                        Section::make([
                            Select::make('category')
                                ->options([
                                    'Animal Sciences',
                                ])
                                ->reactive()
                                ->afterStateUpdated(function ($state, Set $set, Select $component): void {
                                    $set('category_text', $component->getOptions()[$state]);
                                })
                                ->required()
                                ->searchable(),
                            Hidden::make('category_name')
                                ->dehydrated(false),
                            TextInput::make("title")
                                ->required()
                                ->placeholder("Enter title"),
                            TagsInput::make('keywords')
                                ->splitKeys([' ', 'Enter'])
                                ->suggestions(fn (): array => ['Sample'])
                                ->helperText("Press enter or spaces to add a keyword"),
                            Textarea::make("asbtract")
                                ->required()

                                ->rows(4),
                            Checkbox::make('privacy_consent')
                                ->inline()
                                ->required()
                                ->validationAttribute('Privacy Statement')
                                ->accepted()
                                ->label('Yes, I agree to have my data collected and stored according to the privacy statement.'),

                        ])
                            ->heading("Submission Details")
                            ->description("Please provide the following details to help us manage your submission.")
                            ->aside()
                    ]),
                Wizard\Step::make('Upload files')
                    ->schema([
                        Section::make([
                            Repeater::make("submission_files")
                                ->schema([
                                    Select::make('article_type')
                                        ->options([
                                            'Research Article',
                                            'Review',
                                            'Short Report',
                                            'Case Report',
                                        ])
                                        ->searchable()
                                        ->reactive(),
                                    SpatieMediaLibraryFileUpload::make('files')
                                        ->collection('submission_files')
                                        ->statePath('data.files')
                                        ->acceptedFileTypes(['application/pdf', 'image/*', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                        ->multiple()
                                        ->downloadable()
                                        ->previewable()
                                        ->required()
                                        ->reactive()
                                        ->visible(function (Get $get) {
                                            return $get('article_type') !== null;
                                        })
                                ])
                                ->addActionLabel("Add submission file")
                        ])
                            ->heading("Upload files")
                            ->description("Provide any files our editorial team may need to evaluate your submission. In addition to the main work, you may wish to submit data sets, conflict of interest statements, or other supplementary files if these will be helpful for our editors.")
                            ->aside()
                    ]),
                Wizard\Step::make('Add authors')
                    ->schema([
                        Section::make('Authors')
                            ->description("Please provide the following details to help us manage your submission.")
                            ->schema([
                                Repeater::make('authors')
                                    ->label('')
                                    ->itemLabel('Author')
                                    ->schema([
                                        Select::make('author_id')
                                            ->label("Name")
                                            ->searchable()
                                            ->preload()
                                            ->options(
                                                Author::get()
                                                    ->mapWithKeys(function ($author) {
                                                        return [$author->id => $author->getMeta('public_name')];
                                                    })->toArray()
                                            )
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, Set $set, Select $component): void {
                                                $set('author_name', $component->getOptions()[$state]);
                                            }),
                                        Hidden::make('author_name')
                                            ->dehydrated(false),
                                    ])
                                    ->addActionLabel("Add author")
                            ])
                            ->aside()
                    ]),
                Wizard\Step::make('Summary')
                    ->schema([
                        View::make('panel.resources.submission-resource.pages.submission-Summary')
                    ]),
            ])
                ->submitAction(
                    BladeEntry::make('button_save')
                        ->blade('<x-filament::button type="submit" class="ml-auto">Save</x-filament::button>')
                )
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();
        dd($data);
        $submission = SubmissionCreateAction::run($data);
        return redirect()->to(SubmissionResource::getUrl('view', [$submission->id]));
    }

    // protected function getBreadcrumbs(): array
    // {
    //     return [];
    // }
}
