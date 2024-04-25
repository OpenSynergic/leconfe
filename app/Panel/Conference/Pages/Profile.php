<?php

namespace App\Panel\Conference\Pages;

use App\Actions\User\UserUpdateAction;
use App\Models\Enums\UserRole;
use App\Models\User;
use App\Panel\Conference\Livewire\Forms\Conferences\ContributorForm;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'panel.conference.pages.profile';

    protected static bool $shouldRegisterNavigation = false;

    public array $informationFormData = [];
    public array $rolesFormData = [];
    public array $notificationFormData = [];

    public function mount(): void
    {
        $user = auth()->user();
        $meta = $user->getAllMeta()->toArray();

        $this->informationForm->fill([
            ...$user->attributesToArray(),
            'meta' => $meta,
        ]);
        $this->rolesForm->fill([
            'roles' => $user->roles->pluck('name')->toArray(),
            'meta' => $meta,
        ]);
        $this->notificationForm->fill([
            'meta' => $meta,
        ]);
    }

    protected function getForms(): array
    {
        return [
            'informationForm',
            'rolesForm',
            'notificationForm',
        ];
    }

    public function informationForm(Form $form): Form
    {
        return $form
            ->model(auth()->user())
            ->schema([
                Section::make()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('profile')
                            ->label('Profile Photo')
                            ->collection('profile')
                            ->avatar()
                            ->columnSpan(['lg' => 2]),
                        TextInput::make('given_name')
                            ->required(),
                        TextInput::make('family_name'),
                        TextInput::make('email')
                            ->columnSpan(['lg' => 2])
                            ->disabled(fn (?User $record) => $record)
                            ->dehydrated(fn (?User $record) => !$record)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->required(fn (?User $record) => !$record)
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->confirmed(),
                        TextInput::make('password_confirmation')
                            ->requiredWith('password')
                            ->password()
                            ->dehydrated(false),
                        ...ContributorForm::additionalFormField(),
                    ])
                    ->columns(2),
            ])
            ->statePath('informationFormData');
    }

    public function submitInformationForm()
    {
        $this->skipRender();
        try {
            $user = UserUpdateAction::run(auth()->user(), $this->informationForm->getState());
            Notification::make()
                ->success()
                ->title('Saved!')
                ->send();
        } catch (\Throwable $th) {
            Notification::make()
                ->danger()
                ->title('Failed to save.')
                ->send();
        }
    }

    public function rolesForm(Form $form): Form
    {
        return $form
            ->model(auth()->user())
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        CheckboxList::make('roles')
                            ->label('Roles')
                            ->options(UserRole::selfAssignedRoleValues()),
                        TagsInput::make('meta.reviewing_interests')
                            ->placeholder('')
                            ->columnSpan([
                                'lg' => 2,
                            ]),
                    ]),

                
            ])
            ->statePath('rolesFormData');
    }

    public function submitRolesForm()
    {
        $this->skipRender();
        try {
            $data = $this->rolesForm->getState();
            
            $user = auth()->user();
            
            UserUpdateAction::run($user, $data);

            $user->syncRoles($data['roles']);
            
            Notification::make()
                ->success()
                ->title('Saved!')
                ->send();
        } catch (\Throwable $th) {
            Notification::make()
                ->danger()
                ->title('Failed to save.')
                ->send();
            throw $th;
        }
    }

    public function notificationForm(Form $form)
    {
        return $form
            ->statePath('notificationFormData')
            ->schema([
                Section::make('New Announcement')
                    ->description("These are notifications when there's a new announcement send.")
                    ->schema([
                        Checkbox::make('meta.notification.enable_new_announcement_email')
                            ->label('Enable Email Notification'),
                    ])
                    ->aside(),
            ]);
    }

    public function submitNotificationsForm()
    {
        $this->skipRender();
        try {
            $data = $this->notificationForm->getState();
            
            $user = auth()->user();
            
            UserUpdateAction::run($user, $data);
            
            Notification::make()
                ->success()
                ->title('Saved!')
                ->send();
        } catch (\Throwable $th) {
            Notification::make()
                ->danger()
                ->title('Failed to save.')
                ->send();   
            throw $th;
        }
    }


}
