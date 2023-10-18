<?php

namespace App\Panel\Livewire\Workflows;

use App\Panel\Livewire\Workflows\Base\WorkflowStage;
use Awcodes\Shout\Components\Shout;
use Carbon\Carbon;
use Coolsam\FilamentFlatpickr\Enums\FlatpickrTheme;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;

class PeerReviewSetting extends WorkflowStage implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    protected ?string $stage = 'peer-review';

    protected ?string $stageLabel = "Peer Review";

    public function mount()
    {
        $this->form->fill([
            'settings' => [
                'allowed_file_types' => $this->getSetting('allowed_file_types', ['pdf', 'docx', 'doc']),
                'start_at' => $this->getSetting('start_at', now()->addDays(1)->format('d F Y')),
                'end_at' => $this->getSetting('end_at', now()->addDays(14)->format('d F Y')),
            ],
        ]);
    }

    public function submitAction()
    {
        return Action::make('submitAction')
            ->icon("lineawesome-save-solid")
            ->label("save")
            ->successNotificationTitle("Setting saved")
            ->action(function (Action $action) {
                $this->form->validate();
                $data = $this->form->getState();
                foreach ($data['settings'] as $key => $value) {
                    $this->updateSetting($key, $value);
                }
                $action->success();
            });
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Shout::make('stage-closed')
                ->hidden(fn (): bool => $this->isStageOpen())
                ->color('warning')
                ->content("The call for abstracts is not open yet, Start now or schedule opening"),
            Grid::make()
                ->schema([
                    TagsInput::make("settings.allowed_file_types")
                        ->label("Allowed File Types")
                        ->helperText("Allowed file types")
                        ->splitKeys([',', 'enter', ' ']),
                    SpatieMediaLibraryFileUpload::make('settings.paper_templates')
                        ->helperText("Upload paper templates")
                        ->label("Paper templates"),
                    Fieldset::make("Review Deadline")
                        ->schema([
                            Flatpickr::make('settings.start_at')
                                ->dateFormat(setting('format.date'))
                                ->formatStateUsing(function ($state) {
                                    if (blank($state)) {
                                        return null;
                                    }

                                    return Carbon::parse($state)
                                        ->translatedFormat(setting('format.date'));
                                })
                                ->dehydrateStateUsing(fn ($state) => $state ? Carbon::createFromFormat(setting('format.date'), $state) : null)
                                ->label("Date start")
                                ->theme(FlatpickrTheme::DARK),
                            Flatpickr::make('settings.end_at')
                                ->dateFormat(setting('format.date'))
                                ->formatStateUsing(function ($state) {
                                    if (blank($state)) {
                                        return null;
                                    }

                                    return Carbon::parse($state)
                                        ->translatedFormat(setting('format.date'));
                                })
                                ->dehydrateStateUsing(fn ($state) => $state ? Carbon::createFromFormat(setting('format.date'), $state) : null)
                                ->label("Date end")
                                ->theme(FlatpickrTheme::DARK),
                        ])
                ])
                ->columns(1)
        ]);
    }

    public function render()
    {
        return view('panel.livewire.workflows.peer-review-setting');
    }
}
