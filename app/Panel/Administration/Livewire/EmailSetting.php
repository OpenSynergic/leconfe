<?php

namespace App\Panel\Administration\Livewire;

use App\Actions\MailTemplates\MailTemplateRestoreDefaultData;
use App\Actions\Settings\SettingUpdateAction;
use App\Infolists\Components\BladeEntry;
use App\Infolists\Components\VerticalTabs;
use App\Mail\Templates\TestMail;
use App\Models\MailTemplate;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class EmailSetting extends Component implements HasForms, HasInfolists, HasTable
{
    use InteractsWithForms;
    use InteractsWithInfolists;
    use InteractsWithTable;

    public ?array $mailSetupFormData = [];

    public ?array $layoutTemplateFormData = [];

    public function mount()
    {
        $this->mailSetupForm->fill(setting()->all());
        $this->layoutTemplateForm->fill(setting()->all());
    }

    public function render()
    {
        return view('panel.administration.livewire.infolist');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(MailTemplate::query())
            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('subject')
                            ->searchable()
                            ->weight(FontWeight::Medium)
                            ->sortable(),
                        TextColumn::make('description')
                            ->size(TextColumnSize::Small)
                            ->searchable()
                            ->color('gray'),
                        TextColumn::make('key')
                            ->getStateUsing(fn (MailTemplate $record) => Str::afterLast($record->mailable, '\\'))
                            ->badge()
                            ->color('primary'),
                    ]),
                ]),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->color('primary')
                        ->form([
                            TextInput::make('subject')
                                ->required()
                                ->rules('required'),
                            TinyEditor::make('html_template')
                                ->label('Body')
                                ->minHeight(500)
                                ->required()
                                ->profile('email')
                                ->rules('required'),
                        ]),
                    TableAction::make('restoreDefault')
                        ->color('gray')
                        ->successNotificationTitle('Email template restored to default data.')
                        ->icon('heroicon-o-arrow-path')
                        ->label('Restore Default')
                        ->requiresConfirmation()
                        ->failureNotificationTitle('Are you sure you want to restore default data?')
                        ->action(function (MailTemplate $record, TableAction $action) {

                            try {
                                MailTemplateRestoreDefaultData::run($record);
                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                $action->failure();
                            }
                        }),
                ]),
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                VerticalTabs\Tabs::make()
                    ->schema([
                        VerticalTabs\Tab::make('Email Templates')
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                BladeEntry::make('mail-templates')
                                    ->blade('{{ $this->table }}'),
                            ]),
                        VerticalTabs\Tab::make('Layout Templates')
                            ->icon('heroicon-o-bars-3-bottom-left')
                            ->schema([
                                BladeEntry::make('layout-templates')
                                    ->blade('{{ $this->layoutTemplateForm }}'),
                            ]),
                        VerticalTabs\Tab::make('Setup')
                            ->icon('heroicon-o-cog')
                            ->schema([
                                BladeEntry::make('mail-setup')
                                    ->blade('{{ $this->mailSetupForm }}'),
                            ]),

                    ]),
            ]);
    }

    protected function getForms(): array
    {
        return [
            'mailSetupForm',
            'layoutTemplateForm',
        ];
    }

    public function mailSetupForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Email from')
                    ->schema([
                        TextInput::make('mail.from.name')
                            ->required()
                            ->default(config('mail.from.name')),
                        TextInput::make('mail.from.address')
                            ->default(config('mail.from.address'))
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Authorization')
                    ->schema([
                        TextInput::make('mail.auth.host')
                            ->default(config('mail.mailers.smtp.host'))
                            ->required(),
                        TextInput::make('mail.auth.port')
                            ->default(config('mail.mailers.smtp.port'))
                            ->required(),
                        TextInput::make('mail.auth.username')
                            ->default(config('mail.mailers.smtp.username')),
                        TextInput::make('mail.auth.password')
                            ->default(config('mail.mailers.smtp.password'))
                            ->password(),
                        Select::make('mail.auth.encryption')
                            ->default(config('mail.mailers.smtp.encryption'))
                            ->options([
                                null => 'None',
                                'tls' => 'TLS',
                                'ssl' => 'SSL',
                            ])
                            ->default('ssl'),
                    ])
                    ->columns(2),
                Actions::make([
                    Action::make('saveEmailForm')
                        ->label('Save')
                        ->successNotificationTitle('Saved!')
                        ->failureNotificationTitle('Data could not be saved.')
                        ->action(function (Action $action) {
                            $formData = $this->mailSetupForm->getState();
                            try {
                                SettingUpdateAction::run($formData);
                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                $action->failure();
                                // $action->sendFailureNotification();
                            }
                        }),
                    Action::make('testEmail')
                        ->label('Test Email')
                        ->color('gray')
                        ->successNotificationTitle('Success sent test mail to your email.')
                        ->action(function (Action $action) {
                            try {
                                Mail::to(auth()->user()->email)->send(new TestMail());
                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                Notification::make()
                                    ->danger()
                                    ->title('Failed to send test mail to your email.')
                                    ->body($th->getMessage())
                                    ->send();
                            }
                        }),
                ])->alignLeft(),
            ])
            ->statePath('mailSetupFormData');
    }

    public function layoutTemplateForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Layout Template')
                    ->schema([
                        TinyEditor::make('mail.header')
                            ->profile('email'),
                        TinyEditor::make('mail.footer')
                            ->profile('email'),
                    ]),
                Actions::make([
                    Action::make('saveLayoutForm')
                        ->label('Save')
                        ->successNotificationTitle('Saved!')
                        ->failureNotificationTitle('Data could not be saved.')
                        ->action(function (Action $action) {
                            $formData = $this->layoutTemplateForm->getState();
                            try {
                                SettingUpdateAction::run($formData);
                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                $action->failure();
                            }
                        }),
                    Action::make('testEmail')
                        ->label('Test Email')
                        ->color('gray')
                        ->successNotificationTitle('Success sent test mail to your email.')
                        ->action(function (Action $action) {
                            try {
                                Mail::to(auth()->user()->email)->send(new TestMail);
                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                Notification::make()
                                    ->danger()
                                    ->title('Failed to send test mail to your email.')
                                    ->body($th->getMessage())
                                    ->send();
                            }
                        }),
                ])->alignLeft(),
            ])
            ->statePath('layoutTemplateFormData');
    }
}
