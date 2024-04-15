<?php

namespace App\Panel\Administration\Livewire;

use App\Actions\Settings\SettingUpdateAction;
use App\Actions\Site\SiteUpdateAction;
use App\Models\Conference;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class AccessSetting extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $formData = [];

    public Conference $conference;

    public function mount()
    {
        $parsed_url = parse_url(url()->current());
        $path_segments = explode('/', $parsed_url['path']);
        $path_segments = array_values(array_filter($path_segments));
        if (app()->getCurrentConference() == null) {
            $this->form->fill([
                'settings' => [
                    'allow_registration' => app()->getSite()->getMeta('settings.allow_registration'),
                    'must_verify_email' => app()->getSite()->getMeta('settings.must_verify_email'),
                ]
            ]);
            return;
        }
        if (app()->getCurrentConference()->getOriginal('path') === $path_segments[0]) {
            $this->form->fill([
                'settings' => $this->conference->getAllMeta(),
            ]);
            return;
        }
    }

    public function render()
    {
        return view('panel.administration.livewire.form');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Checkbox::make('settings.allow_registration')
                            ->label('Allow Registration')
                            ->helperText('Allow public to register on the site.'),
                        Checkbox::make('settings.must_verify_email')
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
                                SiteUpdateAction::run($formData);
                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                $action->failure();
                            }
                        }),
                ])->alignLeft(),
            ])
            ->statePath('formData');
    }
}
