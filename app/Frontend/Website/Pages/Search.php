<?php

namespace App\Frontend\Website\Pages;

use App\Models\Topic;
use App\Models\Conference;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\Scopes\ConferenceScope;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Search extends Page
{
    use WithPagination;

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
}
