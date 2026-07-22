<?php

namespace App\Panel\Conference\Livewire;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Throwable;
use App\Actions\Conferences\ConferenceUpdateAction;
use App\Facades\License;
use App\Forms\Components\TinyEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class LicenseSetting extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $formData = [];

    public function mount(): void
    {
        $this->form->fill([
            ...app()->getCurrentConference()->attributesToArray(),
            'meta' => app()->getCurrentConference()->getAllMeta(),
        ]);
    }

    public function render()
    {
        return view('forms.form');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model(app()->getCurrentConference())
            ->schema([
                Section::make()
                    ->schema([
                        Radio::make('meta.copyright_holder')
                            ->required()
                            ->options([
                                'author' => __('general.author'),
                                'conference' => __('general.conference'),
                                'custom' => __('general.custom_copyright_holder'),
                            ])
                            ->live(),
                        TextInput::make('meta.custom_copyright_holder')
                            ->required()
                            ->label(__('general.custom_copyright_holder'))
                            ->helperText(__('general.custom_copyright_holder_helper'))
                            ->visible(fn (Get $get) => $get('meta.copyright_holder') === 'custom'),
                        Radio::make('meta.license_url')
                            ->options([
                                ...License::getCCLicenseOptions(),
                                'custom' => __('general.submission_license_custom_url'),
                            ])
                            ->live(),
                        TextInput::make('meta.license_url_custom')
                            ->visible(fn (Get $get) => $get('meta.license_url') === 'custom')
                            ->label(__('general.submission_license_custom_url'))
                            ->url()
                            ->required(),
                        Radio::make('meta.copyright_year')
                            ->label(__('general.copyright_year'))
                            ->helperText(__('general.copyright_year_helper'))
                            ->options([
                                'proceeding' => __('general.copyright_year_use_proceeding_date'),
                                'paper' => __('general.copyright_year_use_paper_date'),
                            ]),
                        TinyEditor::make('meta.license_terms')
                            ->label(__('general.license_terms'))
                            ->helperText(__('general.license_terms_helper')),
                    ]),
                Actions::make([
                    Action::make('save')
                        ->label(__('general.save'))
                        ->successNotificationTitle(__('general.saved'))
                        ->failureNotificationTitle(__('general.data_could_not_saved'))
                        ->action(function (Action $action) {
                            $formData = $this->form->getState();
                            try {
                                ConferenceUpdateAction::run($this->form->getRecord(), $formData);
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
