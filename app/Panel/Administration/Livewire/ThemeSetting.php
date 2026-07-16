<?php

namespace App\Panel\Administration\Livewire;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Throwable;
use App\Facades\Plugin as FacadesPlugin;
use App\Forms\Components\CssFileUpload;
use App\Models\Plugin;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ThemeSetting extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $formData = [];

    public function mount(): void
    {
        $activeTheme = app()->getCurrentTheme();

        $this->form->fill([
            'meta' => [
                'theme' => $activeTheme?->getInfo('folder'),
            ],
            'theme' => $activeTheme?->getFormData() ?? [],
        ]);
    }

    public function render()
    {
        return view('forms.form');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model()
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('meta.theme')
                            ->reactive()
                            ->options(fn () => Plugin::theme()->enabled()->pluck('name', 'id'))
                            ->afterStateUpdated(function (Get $get, &$livewire): void {
                                if (! $get('meta.theme')) {
                                    return;
                                }

                                $livewire->formData['theme'] = FacadesPlugin::getPlugin($get('meta.theme'))?->getFormData();
                            })
                            ->required(),
                        Grid::make(1)
                            ->visible(fn (Get $get) => $get('meta.theme'))
                            ->statePath('theme')
                            ->schema(function (Get $get): array {
                                return FacadesPlugin::getPlugin($get('meta.theme'))?->getFormSchema() ?? [];
                            }),
                        CssFileUpload::make('styleSheet')
                            ->label(__('general.custom_stylesheet'))
                            ->collection('styleSheet')
                            ->getUploadedFileNameForStorageUsing(static function (BaseFileUpload $component, TemporaryUploadedFile $file) {
                                return Str::random().'.css';
                            })
                            ->acceptedFileTypes(['text/css'])
                            ->columnSpan([
                                'xl' => 1,
                                'sm' => 2,
                            ]),
                    ]),
                Actions::make([
                    Action::make('save')
                        ->label(__('general.save'))
                        ->successNotificationTitle(__('general.saved'))
                        ->failureNotificationTitle(__('general.data_could_not_saved'))
                        ->action(function (Action $action) {
                            $formData = $this->form->getState();
                            try {
                                app()->updateCurrentTheme($formData['meta']['theme']);
                                $theme = FacadesPlugin::getPlugin($formData['meta']['theme']);
                                $theme?->saveFormData($formData['theme'] ?? []);

                                $action->sendSuccessNotification();
                            } catch (Throwable $th) {
                                throw $th;
                                $action->sendFailureNotification();
                            }
                        }),
                ])->alignLeft(),

            ])
            ->statePath('formData');
    }
}
