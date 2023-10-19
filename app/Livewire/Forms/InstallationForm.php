<?php

namespace App\Livewire\Forms;

use Carbon\Carbon;
use Livewire\Form;
use App\Models\User;
use App\Models\Conference;
use Livewire\Attributes\Rule;
use App\Models\Enums\UserRole;
use App\Utils\EnvironmentManager;
use Illuminate\Support\Facades\DB;
use App\Actions\User\UserCreateAction;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use App\Actions\Conferences\ConferenceCreateAction;

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

    public $conference_description = null;

    public $testConnectionMessage = null;


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
            'active' => true,
            'meta' => [
                'description' => $this->conference_description,
            ]
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

            // event(new Registered($user));

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
            'database' => '',
        ]);

        if (!empty($this->db_username) && !empty($this->db_password)) {
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

            // buat file env dengan konfigurasi diatas
            app(EnvironmentManager::class)->installation();

            // buat database menggunakan command
            Artisan::call('db:create', ['name' => $this->db_name]);

            // isi file env DATABASE_NAME dengan $db_name
            app(EnvironmentManager::class)->installation([
                'DB_DATABASE' => $this->db_name,
            ]);

            // status pembuatan berhasil
            session()->flash('status', 'Successfully Connected');
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
