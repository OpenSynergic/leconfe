<?php

namespace App\Frontend\Conference\Pages;

use App\Models\Role;
use App\Models\Conference;
use Squire\Models\Country;
use Illuminate\Support\Arr;
use Livewire\Attributes\Rule;
use App\Models\Enums\UserRole;
use Filament\Facades\Filament;
use Livewire\Attributes\Title;
use App\Actions\User\UserCreateAction;
use App\Facades\Setting;
use Illuminate\Auth\Events\Registered;
use Rahmanramsi\LivewirePageGroup\Pages\Page;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;

class Register extends Page
{
    use WithRateLimiting;

    protected static string $view = 'frontend.conference.pages.register';

    public $given_name = null;

    public $family_name = null;

    public $affiliation = null;

    public $country = null;

    public $email = null;

    public $password = null;

    public $password_confirmation = null;

    public $privacy_statement_agree = false;

    public $selfAssignRoles = [];

    public $registerComplete = false;

    public function mount()
    {
        if (Filament::auth()->check()) {
            $this->redirect($this->getRedirectUrl(), navigate: false);
        }

        abort_unless(Setting::get('allow_registration'), 403);
    }

    public function rules()
    {
        $rules =  [
            'given_name' => [
                'required',
            ],
            'family_name' => [
                'nullable',
            ],
            'affiliation' => [
                'nullable',
            ],
            'country' => [
                'nullable',
            ],
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'confirmed',
                'min:8',
            ],
            'privacy_statement_agree' => [
                'required',
            ],
        ];

        if (app()->getCurrentConference()){
            $rules['selfAssignRoles'] = [
                'required',
            ];
        } else {
            $rules['selfAssignRoles'] = [
                'array',
            ];
        }

        return $rules;
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/') => 'Home',
            $this->registerComplete ? 'Register Complete' : 'Register',
        ];
    }

    public function getRedirectUrl(): string
    {
        return app()->getCurrentConference() ? Filament::getPanel()->getUrl() : route('filament.administration.home');
    }

    protected function getViewData(): array
    {
        $data = [
            'countries' => Country::all(),
            'roles' => UserRole::selfAssignedRoleNames(),
            'privacyStatementUrl' => '#',
        ];

        if(!app()->getCurrentConference()){
            $data['conferences'] = Conference::all();
        }

        return $data;
    }

    public function register()
    {
        $data = $this->validate();
        $user = UserCreateAction::run([
            ...Arr::only($data, ['given_name', 'family_name', 'email', 'password']),
            'meta' => Arr::only($data, ['affiliation', 'country']),
        ]);

        if (app()->getCurrentConference()){
            $user->assignRole($data['selfAssignRoles']);
        } else {
            foreach ($data['selfAssignRoles'] as $conferenceId => $roles) {
                // get keys of roles where value is true
                $roles = array_keys(array_filter($roles));
                setPermissionsTeamId($conferenceId);
                $user->assignRole($roles);
            }
            setPermissionsTeamId(null);
        }

        Filament::auth()->login($user);

        session()->regenerate();

        $this->registerComplete = true;
    }
}
