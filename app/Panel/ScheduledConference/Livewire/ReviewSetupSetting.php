<?php

namespace App\Panel\ScheduledConference\Livewire;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Throwable;
use App\Actions\ScheduledConferences\ScheduledConferenceUpdateAction;
use App\Models\Review;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class ReviewSetupSetting extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $formData = [];

    public function mount(): void
    {
        $scheduledConference = app()->getCurrentScheduledConference();

        $this->form->fill([
            ...$scheduledConference->attributesToArray(),
            'meta' => $scheduledConference->getAllMeta(),
        ]);
    }

    public function render()
    {
        return view('forms.form');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model(app()->getCurrentScheduledConference())
            ->schema([
                Section::make()
                    ->schema([
                        Radio::make('meta.review_mode')
                            ->label(__('general.review_mode'))
                            ->options(Review::getModeOptions()),
                        Checkbox::make('meta.default_open_review_for_author'),
                        TextInput::make('meta.review_invitation_response_deadline')
                            ->required()
                            ->label(__('general.default_response_deadline'))
                            ->helperText(__('general.deadline_reviewers_invitations'))
                            ->numeric()
                            ->minValue(1)
                            ->suffix(__('general.days')),
                        TextInput::make('meta.review_completion_deadline')
                            ->required()
                            ->label(__('general.default_completion_deadline'))
                            ->helperText(__('general.default_completing_deadline'))
                            ->numeric()
                            ->minValue(1)
                            ->afterOrEqual('meta.review_invitation_response_deadline')
                            ->suffix(__('general.days')),
                    ]),
                Actions::make([
                    Action::make('save')
                        ->label(__('general.save'))
                        ->successNotificationTitle(__('general.saved'))
                        ->failureNotificationTitle(__('general.data_could_not_saved'))
                        ->action(function (Action $action) {
                            $formData = $this->form->getState();
                            try {
                                ScheduledConferenceUpdateAction::run(app()->getCurrentScheduledConference(), $formData);
                            } catch (Throwable $th) {
                                $action->sendFailureNotification();
                                $action->halt();
                            }
                        }),
                ])->alignLeft(),

            ])
            ->statePath('formData');
    }
}
