<?php

namespace App\Livewire\Forms\Installation;

use App\Actions\User\UserCreateAction;
use App\Models\Enums\UserRole;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use Livewire\Form;

class AccountForm extends Form
{
    #[Rule('required')]
    public $given_name = null;

    #[Rule('required')]
    public $family_name = null;

    #[Rule('required|email')]
    public $email = null;

    #[Rule('required|confirmed')]
    public $password = null;

    #[Rule('required')]
    public $password_confirmation = null;

    public function process()
    {
        try {
            DB::beginTransaction();

            $user = UserCreateAction::run($this->all());

            $user->assignRole(UserRole::Admin->value);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
