<?php

namespace App\Frontend\Website\Pages;

use App\Models\Conference;
use App\Models\Scopes\ConferenceScope;
use App\Models\Topic;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Search extends Page
{
    use WithPagination;

    protected static ?string $title = 'Search Conferences';

    protected static string $view = 'frontend.website.pages.search';

    #[Url(except: "")]
    public string $query = "";

    #[Url(except: "")]
    public string $topic = "";

    public function mount()
    {
    }

    protected function getViewData(): array
    {
        return [
            'isAdvancedSearch' => !empty($this->topic),
            'topics' => Topic::withoutGlobalScope(ConferenceScope::class)
                ->distinct()
                ->get(),
            'searchResults' => Conference::query()
                ->when($this->query, fn ($query) => $query->where('name', 'like', "%{$this->query}%"))
                ->when($this->topic, fn ($query) => $query->whereIn('id', Topic::query()
                    ->withoutGlobalScope(ConferenceScope::class)
                    ->where('name', $this->topic)
                    ->pluck('conference_id')
                    ->toArray()))
                ->with(['media', 'meta'])
                ->paginate(6),
        ];
    }

    public function clearAllSearch()
    {
        $this->query = '';
        $this->topic = '';
    }

    public function updatingQuery()
    {
        $this->resetPage();
    }

    protected function getLayoutData(): array
    {
        return [
            'title' => $this->getTitle()
        ];
    }
}
