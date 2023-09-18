<?php

namespace App\Website\Pages;

use App\Actions\User\UserCreateAction;
use App\Livewire\Forms\RegisterAccount;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Arr;
use Livewire\Attributes\Rule;
use Rahmanramsi\LivewirePageGroup\Pages\Page;
use Squire\Models\Country;

class Register extends Page
{
    use WithRateLimiting;

    protected static string $view = 'website.pages.register';

    #[Rule('required')]
    public $given_name = null;

    #[Rule('nullable')]
    public $family_name = null;
    
    #[Rule('nullable')]
    public $affiliation = null;
    
    #[Rule('nullable')]
    public $country = null;

    #[Rule('required|email')]
    public $email = null;

    #[Rule('required|confirmed|min:8')]
    public $password = null;

    #[Rule('required')]
    public $password_confirmation = null;

    #[Rule('required')]
    public $privacy_statement_agree = false;


    public function mount()
    {
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/') => 'Home',
            'Register',
        ];
    }

    protected function getViewData(): array
    {
        return [
            'countries' => Country::all()
        ];
    }

    public function register()
    {
        $data = $this->validate();


        $user = UserCreateAction::run([
            ...Arr::only($data, ['given_name', 'family_name', 'email', 'password']),
            'meta' => Arr::only($data, ['affiliation', 'country']),
        ]);
        
        event(new Registered($user));

        Filament::auth()->login($user);

        session()->regenerate();

        return app(RegistrationResponse::class);
    }
}
