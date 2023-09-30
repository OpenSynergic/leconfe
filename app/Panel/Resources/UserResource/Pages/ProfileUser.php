<?php

namespace App\Panel\Resources\UserResource\Pages;

use App\Actions\User\UserUpdateAction;
use App\Infolists\Components\BladeEntry;
use App\Panel\Resources\UserResource;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class ProfileUser extends Page
{
    use InteractsWithRecord;

    protected static string $resource = UserResource::class;

    public array $informationFormData = [];

    public array $notificationFormData = [];

    /**
     * @var view-string
     */
    protected static string $view = 'panel.resources.user-resource.pages.profile-user';

    public function mount($user_id = null): void
    {
        $this->record = $user_id ? $this->resolveRecord($user_id) : auth()->user();

        abort_unless(static::getResource()::canEdit($this->getRecord()), 403);

        $this->informationForm->fill([
            ...$this->getRecord()->attributesToArray(),
            'meta' => $this->getRecord()->getAllMeta()->toArray(),
        ]);

        $this->notificationForm->fill([
            // ...$this->getRecord()->attributesToArray(),
            'meta' => $this->getRecord()->getAllMeta()->toArray(),
        ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->getRecord())
            ->schema([
                Tabs::make('Label')
                    ->tabs([
                        Tabs\Tab::make('Informations')
                            ->schema([
                                BladeEntry::make('informationForm')
                                    ->blade('{{ $this->informationForm }}'),
                            ]),
                        Tabs\Tab::make('Notifications')
                            ->schema([
                                BladeEntry::make('notificationForm')
                                    ->blade('{{ $this->notificationForm }}'),
                            ]),
                    ])
                    ->contained(false)
                    ->persistTabInQueryString(),
            ]);
    }

    protected function getForms(): array
    {
        return [
            'informationForm',
            'notificationForm',
        ];
    }

    public function notificationForm(Form $form)
    {
        return $form
            ->statePath('notificationFormData')
            ->schema([
                Section::make('New Announcement')
                    ->description("These are notifications when there's a new announcement send.")
                    ->schema([
                        Checkbox::make('meta.receive_email_new_announcement')
                            ->label('Enable Email Notification'),
                    ])
                    ->aside(),
                Actions::make([
                    Action::make('saveNotification')
                        ->label('Save')
                        ->successNotificationTitle('Saved!')
                        ->failureNotificationTitle('Data could not be saved.')
                        ->action(function (Action $action) {
                            $formData = $this->notificationForm->getState();

                            try {
                                UserUpdateAction::run($this->getRecord(), $formData);
                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                $action->failure();
                                // $action->sendFailureNotification();
                            }
                        }),
                ])->alignLeft(),
            ]);
    }

    public function informationForm(Form $form)
    {
        $form = static::getResource()::form($form);

        return $form
            ->statePath('informationFormData')
            ->schema([
                ...$form->getComponents(),
                Actions::make([
                    Action::make('save')
                        ->successNotificationTitle('Saved!')
                        ->failureNotificationTitle('Data could not be saved.')
                        ->action(function (Action $action) {
                            $formData = $this->informationForm->getState();
                            try {
                                UserUpdateAction::run($this->getRecord(), $formData);
                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                // $action->failure();
                                $action->cancel();
                                // $action->sendFailureNotification();
                            }
                        }),
                ])->alignLeft(),
            ])
            ->model($this->getRecord());
    }
}
