<?php

namespace App\Panel\Pages\Settings;

use App\Infolists\Components\BladeEntry;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Blade;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class Conference extends Page implements HasInfolists, HasForms
{
    use InteractsWithInfolists, InteractsWithForms;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'panel.pages.settings.conference';

    protected ?string $heading = 'Conference Settings';

    public array $generalFormData = [];

    public function mount()
    {
        $this->generalForm->fill([
            ...Filament::getTenant()->attributesToArray(),
            // 'meta' => Filament::getTenant()->getAllMeta()->toArray(),
        ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('conference_settings')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->icon('heroicon-m-window')
                            ->schema([
                                BladeEntry::make('general')
                                    ->blade('{{ $this->generalForm }}')
                            ]),
                    ])
                    ->contained(false),
            ]);
    }

    protected function getForms(): array
    {
        return [
            'generalForm',
        ];
    }

    public function generalForm(Form $form): Form
    {
        return $form
            ->statePath('generalFormData')
            ->model(Filament::getTenant())
            ->schema([
                FormSection::make()
                    ->schema([
                        FormSection::make('Information')
                            ->aside()
                            ->description('General Information about the conference.')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('logo')
                                    ->collection('logo')
                                    ->image()
                                    ->conversion('thumb'),
                                TinyEditor::make('meta.page_footer'),
                            ]),
                        Actions::make([
                            Action::make('save')
                                ->icon('heroicon-m-star')
                                ->requiresConfirmation()
                                ->action(fn() => dd($this)),
                        ])->alignRight(),
                    ])
            ]);
    }
}
