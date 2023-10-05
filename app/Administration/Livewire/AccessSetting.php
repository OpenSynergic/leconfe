<?php

namespace App\Administration\Livewire;

use App\Actions\MailTemplates\MailTemplatePopulateDefaultData;
use App\Actions\MailTemplates\MailTemplateRestoreDefaultData;
use App\Actions\Settings\SettingUpdateAction;
use App\Infolists\Components\BladeEntry;
use App\Infolists\Components\VerticalTabs;
use App\Mail\Templates\TestMail;
use App\Mail\Templates\VerifyUserEmail;
use App\Models\MailTemplate;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
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

class AccessSetting extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $formData = [];

    public function mount()
    {
        $this->form->fill(setting()->all());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Checkbox::make('allow_registration')
                            ->label('Allow Registration')
                            ->helperText('Allow public to register on the site.'),
                        Checkbox::make('must_verify_email')
                            ->label('Must Verify Email')
                            ->helperText('Require users to verify their email address before they can log in.'),
                    ])
                    ->columns(1),
                Actions::make([
                    Action::make('save')
                        ->label('Save')
                        ->successNotificationTitle('Saved!')
                        ->failureNotificationTitle('Data could not be saved.')
                        ->action(function (Action $action) {
                            $formData = $this->form->getState();
                            try {
                                SettingUpdateAction::run($formData);
                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                $action->failure();
                                // $action->sendFailureNotification();
                            }
                        }),
                ])->alignLeft(),
            ])
            ->statePath('formData');
    }

    public function render()
    {
        return view('administration.livewire.access-setting');
    }
}
