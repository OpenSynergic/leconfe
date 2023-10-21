<?php

namespace App\Panel\Livewire\Submissions\Components\Files\Traits;

use App\Infolists\Components\LivewireEntry;
use App\Panel\Livewire\Submissions\Components\Files\SelectFiles;
use Awcodes\Shout\Components\ShoutEntry;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;

trait CanSelectFiles
{
    abstract function getTargetCategory(): string;

    abstract function getSelectableCategories(): array;

    public function uploadAction()
    {
        return ActionGroup::make([
            Action::make('select-files')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel("Close")
                ->icon("iconpark-check")
                ->label('Select Files')
                ->extraAttributes([
                    'x-on:close-select-files.window' => new HtmlString('$wire.unmountTableAction(\'select-files\')')
                ])
                ->infolist([
                    ShoutEntry::make('information')
                        ->color("info")
                        ->content("Choose the files to create duplicates."),
                    LivewireEntry::make('list-files')
                        ->livewire(
                            SelectFiles::class,
                            [
                                'submission' => $this->submission,
                                'targetCategory' => $this->getTargetCategory(),
                                'selectableCategories' => $this->getSelectableCategories(),
                                'lazy' => true
                            ]
                        )
                ]),
            Action::make('upload')
                ->icon("iconpark-upload")
                ->label("Upload Files")
                ->hidden(
                    fn (): bool => $this->submission->isDeclined() ?: $this->isViewOnly()
                )
                ->modalWidth('xl')
                ->form(
                    $this->uploadFormSchema()
                )
                ->successNotificationTitle('Files added successfully')
                ->failureNotificationTitle('There was a problem adding the files')
                ->action(
                    fn (array $data, Action $action) => $this->handleUploadAction($data, $action)
                )
        ])
            ->button();
    }
}
