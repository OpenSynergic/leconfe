<?php

namespace App\Panel\Conference\Pages;

use App\Actions\User\UserUpdateAction;
use App\Infolists\Components\BladeEntry;
use App\Models\Enums\UserRole;
use App\Models\User;
use App\Panel\Conference\Livewire\Forms\Conferences\ContributorForm;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Tabs;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Infolist;
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

    public function profileInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make()
                    ->schema([
                        Tab::make('Information')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                BladeEntry::make('information-form')
                                    ->blade('
                                        <form wire:submit="submitInformationForm" class="space-y-4">
                                            {{ $this->informationForm }}
                            
                                            <x-filament::button type="submit">
                                                Save
                                            </x-filament::button>                
                                        </form>
                                    '),
                            ]),
                        Tab::make('Roles')
                            ->icon('heroicon-o-shield-check')
                            ->hidden(!app()->getCurrentConference())
                            ->schema([
                                BladeEntry::make('roles-form')
                                    ->blade('
                                        <form wire:submit="submitRolesForm" class="space-y-4">
                                            @if (empty(auth()->user()->roles->pluck("name")->toArray()))
                                            <x-filament::section class="!bg-primary-100">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <x-heroicon-o-globe-alt class="w-8 h-8 text-primary-800" />
                                                        <div class="flex flex-col ml-3">
                                                            <div class="font-medium text-sm leading-none">Register for the conference and select your preferred role to participate.</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </x-filament::section>
                                            @endif
                                            {{ $this->rolesForm }}
                            
                                            <x-filament::button type="submit">
                                                Save
                                            </x-filament::button>                
                                        </form>
                                    '),
                            ]),
                        Tab::make('Notifications')
                            ->icon('heroicon-o-bell')
                            ->schema([
                                BladeEntry::make('notification-form')
                                    ->blade('
                                        <form wire:submit="submitNotificationsForm" class="space-y-4">
                                            <x-filament::section>
                                                {{ $this->notificationForm }}
                                            </x-filament::section>

                                            <x-filament::button type="submit">
                                                Save
                                            </x-filament::button>                
                                        </form>
                                    '),
                            ]),
                    ])
                    ->contained(false)
                    ->persistTab()
                    ->persistTabInQueryString()
            ]);
    }
}