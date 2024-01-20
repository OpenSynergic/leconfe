<?php

namespace App\Livewire\Forms;

use App\Models\Conference;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Rule;
use Livewire\Form;

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
    public $db_name = 'leconfe';

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

    public function checkDatabaseConnection(): bool
    {
        try {
            $this->reconnectDbWithNewData();
        } catch (\Throwable $th) {
            $this->addError('databaseOperationError', 'Connection failed: '.$th->getMessage());

            return false;
        }

        return true;
    }

    public function createDatabase(): bool
    {
        $dbName = $this->db_name;

        try {

            $this->reconnectDbWithNewData();

            if (! $this->checkDatabaseExists($dbName)) {
                Schema::createDatabase($dbName);
            }
        } catch (\Throwable $th) {
            $this->addError('databaseOperationError', 'Create database failed: Please manually create your database '.$th->getMessage());

            return false;
        }

        return true;
    }

    private function prepareDatabaseConnection(): array
    {
        $connectionArray = config("database.connections.{$this->db_connection}", []);

        $connectionArray = array_merge($connectionArray, [
            'driver' => $this->db_connection,
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

        return $connectionArray;
    }

    private function checkDatabaseExists($dbName): bool
    {
        return ! empty(DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'"));
    }

    protected function reconnectDbWithNewData()
    {
        $connectionArray = $this->prepareDatabaseConnection();

        Config::set("database.connections.{$this->db_connection}", $connectionArray);

        DB::purge();

        // reconnect to database with new settings
        DB::reconnect();

        DB::connection()->getPdo();
    }
}
