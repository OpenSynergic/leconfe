<?php

namespace App\Panel\Pages\Settings;

use App\Actions\Settings\SettingUpdateAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Pages\Page;

class Workflow extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Settings';

    // TODO carikan icon untuk halaman workflow
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static string $view = 'panel.pages.settings.workflow';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(setting()->all());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Label')
                    ->tabs([
                        Tabs\Tab::make('Submission')
                            ->schema([
                                Placeholder::make('disable_submission_label')
                                    ->label('')
                                    ->content('Prevent user from submitting new paper to conferences.'),
                                Toggle::make('disable_submission')
                                    ->label('Disable Submission'),
                            ]),
                        Tabs\Tab::make('Review')
                            ->schema([
                                // ...
                            ]),
                    ]),

            ])
            ->statePath('data');
    }

    public function submit()
    {
        SettingUpdateAction::run($this->form->getState());
    }
}
