<?php

namespace App\Livewire\Forms;

use App\Actions\Conferences\ConferenceCreateAction;
use App\Actions\User\UserCreateAction;
use App\Models\Conference;
use App\Models\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use Livewire\Form;

class InstallationForm extends Form
{
    /**
     * Field for Account
     */
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

    /**
     * Field for Database
     */
    #[Rule('required')]
    public $db_connection = 'mysql';

    #[Rule('required')]
    public $db_username = null;

    #[Rule('required')]
    public $db_password = null;

    #[Rule('required')]
    public $db_name = null;

    #[Rule('required')]
    public $db_host = '127.0.0.1';

    #[Rule('required')]
    public $db_port = '3306';

    /**
     * Field for Conference
     */
    #[Rule('required')]
    public $conference_name = null;

    #[Rule('required')]
    public $conference_type = 'Offline';

    /**
     * Field for Timezone
     */
    #[Rule('required')]
    public $timezone = 'Asia/Makassar';

    public function createConference(): Conference
    {
        return ConferenceCreateAction::run([
            'name' => $this->conference_name,
            'type' => $this->conference_type,
            'current' => true,
        ]);
    }

    public function createAccount(): User
    {
        try {
            DB::beginTransaction();

            $user = UserCreateAction::run($this->only([
                'given_name',
                'family_name',
                'email',
                'password',
            ]));

            $user->assignRole(UserRole::Admin->value);

            event(new Registered($user));

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $user;
    }

    public function checkDatabaseConnection(): bool
    {
        $connection = $this->db_connection;

        $settings = config("database.connections.$connection");

        $connectionArray = array_merge($settings, [
            'driver' => $connection,
            'database' => $this->db_name,
        ]);

        if (! empty($this->db_username) && ! empty($this->db_password)) {
            $connectionArray = array_merge($connectionArray, [
                'username' => $this->db_username,
                'password' => $this->db_password,
                'host' => $this->db_host,
                'port' => $this->db_port,
            ]);
        }

        Config::set("database.connections.$connection", $connectionArray);

        try {
            // reconnect to database with new settings
            DB::reconnect();

            DB::connection()->getPdo();
        } catch (\Throwable $th) {
            $this->addError('checkConnection', $th->getMessage());

            return false;
        }

        return true;
    }

    public function migrate()
    {
        Artisan::call('optimize:clear');
        Artisan::call('storage:link');
        Artisan::call('migrate:fresh --force --seed');
    }

    public function updateConfig()
    {
        Config::set('app.timezone', $this->timezone);
    }

    public function process()
    {
        $this->migrate();
        $this->createAccount();
        $this->createConference();
    }
}
