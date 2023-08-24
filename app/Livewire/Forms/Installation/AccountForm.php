<?php

namespace App\Livewire\Forms\Installation;

use App\Actions\User\UserCreateAction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use Livewire\Form;

class AccountForm extends Form
{
    #[Rule('required')]
    public $given_name = 'Rahman';

    #[Rule('required')]
    public $family_name = 'Ramsi';

    #[Rule('required|email')]
    public $email = 'rahmanramsi19@gmail.com';

    #[Rule('required|confirmed')]
    public $password = 'password';

    #[Rule('required')]
    public $password_confirmation = 'password';

    public function process()
    {
        try {
            DB::beginTransaction();
            
            UserCreateAction::run($this->all());

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
