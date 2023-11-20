<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\User;
use App\Models\Conference;
use Livewire\Attributes\Rule;
use App\Models\Enums\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use App\Actions\User\UserCreateAction;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use App\Actions\Conferences\ConferenceCreateAction;

class InstallationForm extends Form
{
    /**
     * Field for Account
     */
    #[Rule('required', onUpdate: false)]
    public $given_name = null;

    #[Rule('required', onUpdate: false)]
    public $family_name = null;

    #[Rule('required|email', onUpdate: false)]
    public $email = null;

    #[Rule('required|confirmed', onUpdate: false)]
    public $password = null;

    #[Rule('required', onUpdate: false)]
    public $password_confirmation = null;

    /**
     * Field for Database
     */
    #[Rule('required', onUpdate: false)]
    public $db_connection = 'mysql';

    #[Rule('required', onUpdate: false)]
    public $db_username = null;

    #[Rule('required', onUpdate: false)]
    public $db_password = null;

    #[Rule('required', onUpdate: false)]
    public $db_name = null;

    #[Rule('required', onUpdate: false)]
    public $db_host = '127.0.0.1';

    #[Rule('required', onUpdate: false)]
    public $db_port = '3306';

    /**
     * Field for Conference
     */
    #[Rule('required', onUpdate: false)]
    public $conference_name = null;

    #[Rule('required', onUpdate: false)]
    public $conference_type = 'Offline';

    public $conference_description = null;

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
            ],
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

            DB::commit();
        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }

        return $user;
    }

    public function checkDatabaseConnection(): bool
    {
        $connectionArray = $this->prepareDatabaseConnection();

        Config::set("database.connections.{$this->db_connection}", $connectionArray);

        try {
            // reconnect to database with new settings
            DB::reconnect();

            DB::connection()->getPdo();

            session()->flash('success', 'Successfully Connected');
        } catch (\Throwable $th) {
            $this->addError('databaseOperationError', 'Connection failed: ' . $th->getMessage());

            return false;
        }

        return true;
    }

    public function createDatabase(): bool
    {
        $dbName = $this->db_name;

        try {

            $databaseExists = $this->checkDatabaseExists($dbName);

            if (!$databaseExists) {
                $this->createNewDatabase($dbName);
            }

            $this->updateDatabaseConfig($dbName);

            DB::reconnect();

            DB::connection()->getPdo();

            session()->flash('success', 'Connection success and database successfully created');
        } catch (\Throwable $th) {
            $this->addError('databaseOperationError', 'Create database failed: Please manually create your database ' . $th->getMessage());

            return false;
        }

        return true;
    }

    private function prepareDatabaseConnection(): array
    {
        $connectionArray = config("database.connections.{$this->db_connection}", []);

        $connectionArray = array_merge($connectionArray, [
            'driver' => $this->db_connection,
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

        return $connectionArray;
    }

    private function checkDatabaseExists($dbName): bool
    {
        return !empty(DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'"));
    }

    private function createNewDatabase($dbName): void
    {
        Schema::createDatabase($dbName);
    }

    private function updateDatabaseConfig($dbName): void
    {
        Config::set('database.connections.mysql.database', $dbName);
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
