<?php

namespace CustomSidebarManager\Pages;

use App\Facades\Plugin;
use App\Tables\Columns\IndexColumn;
use CustomSidebarManager\Models\CustomSidebar;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Kra8\Snowflake\Snowflake;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class CustomSidebarManagerPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $title = 'Custom Sidebar Manager';

    protected static string $view = 'CustomSidebarManager::page';

    protected static bool $shouldRegisterNavigation = false;

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            route('filament.conference.resources.plugins.index', ['tenant' => App::getCurrentConference()]) => 'Plugins',
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(CustomSidebar::query())
            ->columns([
                IndexColumn::make('no'),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
            ])
            ->actions([
                TableAction::make('edit')
                    ->label('Edit')
                    ->modalWidth('3xl')
                    ->fillForm(function (Model $record, Table $table): array {
                        $data = $record->attributesToArray();

                        return $data;
                    })
                    ->form($this->getFormSchemas())
                    ->action(function ($record, array $data) {
                        $record->name = $data['name'];
                        $record->content = $data['content'];
                        $record->show_name = $data['show_name'] ?? false;

                        $plugin = Plugin::getPlugin('CustomSidebarManager');
                        $blocks = $plugin->getSetting('blocks', []);

                        foreach ($blocks as $key => $block) {
                            if ($block['id'] == $record->id) {
                                $blocks[$key] = $record->toArray();
                            }
                        }

                        $plugin->updateSetting('blocks', $blocks);
                    }),
                TableAction::make('delete')
                    ->label('Delete')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $plugin = Plugin::getPlugin('CustomSidebarManager');
                        $blocks = $plugin->getSetting('blocks', []);

                        foreach ($blocks as $key => $block) {
                            if ($block['id'] == $record->id) {
                                unset($blocks[$key]);
                            }
                        }
                        $plugin->updateSetting('blocks', array_values($blocks));
                    }),
            ])
            ->bulkActions([
                // ...
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Create Custom Sidebar')
                ->action(function (array $data) {
                    $data['id'] = app(Snowflake::class)->short();

                    $plugin = Plugin::getPlugin('CustomSidebarManager');
                    $blocks = $plugin->getSetting('blocks', []);
                    $blocks[] = $data;

                    $plugin->updateSetting('blocks', $blocks);
                })
                ->modalWidth('3xl')
                ->form($this->getFormSchemas()),
        ];
    }

    public function getFormSchemas(): array
    {
        return [
            TextInput::make('name')
                ->label('Name')
                ->required(),
            Toggle::make('show_name')
                ->label('Show the name of this sidebar above the content?')
                ->default(false),
            TinyEditor::make('content')
                ->label('Content')
                ->minHeight(300)
                ->helperText('Content of the sidebar.'),
        ];
    }
}
