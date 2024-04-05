<?php

namespace App\Frontend\Website\Pages;

use App\Models\Conference;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Search extends Page
{
    use WithPagination;

    protected static ?string $title = 'Search Conferences';

    protected static string $view = 'frontend.website.pages.search';

    #[Url(except: '')] 
    public string $query = '';

    public function mount()
    {
    }

    protected function getViewData(): array
    {
        return [
            'searchResults' => Conference::query()
                ->when($this->query, fn($query) => $query->where('name', 'like', "%{$this->query}%"))
                ->with(['media', 'meta'])
                ->paginate(6),
        ];
    }

    public function updatingQuery()
    {
        $this->resetPage();
    }
}
