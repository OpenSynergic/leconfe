<?php

namespace App\Panel\ScheduledConference\Livewire\Submissions\Forms;

use Livewire\Component;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use App\Actions\Submissions\SubmissionUpdateAction;
use App\Forms\Components\TinyEditor;
use App\Models\Submission;
use App\Utils\TinyMceWordCounter;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Stevebauman\Purify\Facades\Purify;

class Detail extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Submission $submission;

    public array $meta = [];

    public array $topics = [];

    public function mount(Submission $submission)
    {
        $this->form->fill([
            'topics' => $this->submission->topics()->pluck('id')->toArray(),
            'meta' => $this->submission->getAllMeta()->toArray(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $abstractWordLimit = (int) ($this->submission?->track?->getMeta('abstract_word_count') ?? 0);

        return $schema
            ->disabled(function (): bool {
                return ! auth()->user()->can('editing', $this->submission);
            })
            ->model($this->submission)
            ->schema([
                Toggle::make('meta.paper_published_on_external')
                    ->label(__('general.paper_published_on_external'))
                    ->reactive(),
                TextInput::make('meta.paper_external_url')
                    ->label(__('general.paper_external_url'))
                    ->visible(fn (Get $get) => $get('meta.paper_published_on_external'))
                    ->url()
                    ->required()
                    ->placeholder('https://'),
                TextInput::make('meta.title')
                    ->label(__('general.title')),
                TextInput::make('meta.subtitle')
                    ->label(__('general.subtitle')),
                Select::make('topics')
                    ->preload()
                    ->multiple()
                    ->maxItems(fn (): ?int => $this->topicSelectionLimit())
                    ->helperText(fn (): ?string => $this->topicSelectionLimitHelperText())
                    ->relationship('topics', 'name')
                    ->label(__('general.topic'))
                    ->searchable(),
                TagsInput::make('meta.keywords')
                    ->label(__('general.keywords'))
                    ->splitKeys([','])
                    ->placeholder(''),
                TinyEditor::make('meta.abstract')
                    ->label(__('general.abstract'))
                    ->required()
                    ->minHeight(300)
                    ->rule(fn (): Closure => function (string $attribute, $value, Closure $fail) use ($abstractWordLimit) {
                        if ($abstractWordLimit < 1 || blank($value)) {
                            return;
                        }

                        if (TinyMceWordCounter::countWords($value) > $abstractWordLimit) {
                            $fail(__('general.abstract_word_limit_exceeded', ['count' => $abstractWordLimit]));
                        }
                    })
                    ->dehydrateStateUsing(fn (?string $state) => Purify::clean($state)),
            ]);
    }

    public function submit(): void
    {

        SubmissionUpdateAction::run(
            $this->form->getState(),
            $this->submission
        );

        Notification::make()
            ->body(__('general.saved_successfuly'))
            ->success()
            ->send();
    }

    public function render()
    {
        return view('panel.scheduledConference.livewire.submissions.forms.detail');
    }

    private function topicSelectionLimit(): ?int
    {
        return $this->submission
            ->scheduledConference()
            ->first()
            ?->getSubmissionTopicSelectionLimit();
    }

    private function topicSelectionLimitHelperText(): ?string
    {
        $limit = $this->topicSelectionLimit();

        return $limit === null
            ? null
            : __('general.select_up_to_topics', ['count' => $limit]);
    }
}
