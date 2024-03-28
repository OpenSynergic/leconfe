<?php

namespace App\Panel\Conference\Resources\Conferences;

use App\Actions\Speakers\SpeakerCreateAction;
use App\Actions\Speakers\SpeakerDeleteAction;
use App\Actions\Speakers\SpeakerUpdateAction;
use App\Models\Speaker;
use App\Models\SpeakerPosition;
use App\Models\SpeakerRole;
use App\Panel\Conference\Livewire\Forms\Conferences\ContributorForm;
use App\Panel\Conference\Resources\Conferences\SpeakerResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SpeakerResource extends Resource
{
    protected static ?string $navigationGroup = 'Conferences';

    protected static ?string $model = Speaker::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return 'Speakers';
    }

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()
            ->orderBy('order_column')
            ->with([
                'role',
                'media',
                'meta',
            ])
            ->whereHas('role');
    }

    public static function getModelLabel(): string
    {
        return 'Speaker';
    }

    public static function selectSpeakerField($form): Select
    {
        return Select::make('speaker_id')
            ->label('Select Existing Speaker')
            ->placeholder('Select Speaker')
            ->preload()
            ->native(false)
            ->searchable()
            ->allowHtml()
            ->options(function () {
                $speakers = static::getEloquentQuery()->pluck('email')->toArray();

                return Speaker::query()
                    ->whereNotIn('email', $speakers)
                    ->get()
                    ->mapWithKeys(fn (Speaker $speaker) => [$speaker->getKey() => static::renderSelectSpeaker($speaker)])
                    ->toArray();
            })
            ->optionsLimit(10)
            ->getSearchResultsUsing(
                function (string $search) {
                    $speakers = static::getEloquentQuery()->pluck('email')->toArray();

                    return Speaker::query()
                        ->with(['media', 'meta'])
                        ->whereNotIn('email', $speakers)
                        ->where(fn ($query) => $query->where('given_name', 'LIKE', "%{$search}%")
                            ->orWhere('family_name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%"))
                        ->get()
                        ->mapWithKeys(fn (Speaker $speaker) => [$speaker->getKey() => static::renderSelectSpeaker($speaker)])
                        ->toArray();
                }
            )
            ->live()
            ->afterStateUpdated(function ($state) use ($form) {
                if (! $state) {
                    return;
                }
                $speaker = Speaker::with(['meta', 'role' => fn ($query) => $query->withoutGlobalScopes()])->findOrFail($state);
                $role = SpeakerRoleResource::getEloquentQuery()->whereName($speaker?->role?->name)->first();

                $formData = [ 
                    'speaker_id' => $state,
                    'given_name' => $speaker->given_name,
                    'family_name' => $speaker->family_name,
                    'email' => $speaker->email,
                    'speaker_role_id' => $role->id ?? null,
                    'meta' => $speaker->getAllMeta()
                ];
                return static::form($form)->fill($formData);
            })
            ->columnSpanFull();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                static::selectSpeakerField($form),
                ...ContributorForm::generalFormField(app()->getCurrentConference()),
                Forms\Components\Select::make('speaker_role_id')
                    ->label('Role')
                    ->required()
                    ->searchable()
                    ->relationship(
                        name: 'role',
                        titleAttribute: 'name',
                    )
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required(),
                    ])
                    ->createOptionAction(
                        fn (FormAction $action) => $action->color('primary')
                            ->modalWidth('xl')
                            ->modalHeading('Create Speaker Position')
                            ->mutateFormDataUsing(function (array $data): array {
                                return $data;
                            })
                            ->form(function (Select $component, Form $form): array|Form|null {
                                return SpeakerRoleResource::form($form);
                            })
                    )
                    ->columnSpan([
                        'lg' => 2,
                    ]),
                ...ContributorForm::additionalFormField(app()->getCurrentConference()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order_column')
            ->heading('Speakers Table')
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-user-plus')
                    ->modalWidth('2xl')
                    ->using(fn (array $data) => SpeakerCreateAction::run($data)),
            ])
            ->columns([
                ...ContributorForm::generalTableColumns(),
            ])
            ->actions([
                ...ContributorForm::tableActions(SpeakerUpdateAction::class, SpeakerDeleteAction::class),
            ])
            ->filters([
                // SelectFilter::make('position')
                //     ->relationship('position', 'name'),
            ]);
    }

    public static function renderSelectSpeaker(Speaker $speaker): string
    {
        $speaker->load('conference');
        return view('forms.select-contributor-conference', ['contributor' => $speaker])->render();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSpeakers::route('/'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            // Widgets\SpeakerOverview::make(),
        ];
    }
}
