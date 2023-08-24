<?php

namespace App\Livewire\Forms\Installation;

use App\Utils\EnvironmentManager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Rule;
use Livewire\Form;

class DatabaseForm extends Form
{
    #[Rule('required')]
    public $connection = 'mysql';

    #[Rule('required')]
    public $username = null;

    #[Rule('required')]
    public $password = null;

    #[Rule('required')]
    public $name = null;

    #[Rule('required')]
    public $host = '127.0.0.1';

    #[Rule('required')]
    public $port = '3306';


    public function checkConnection()
    {
        $connection = $this->connection;

        $settings = config("database.connections.$connection");

        $connectionArray = array_merge($settings, [
            'driver' => $connection,
            'database' => $this->name,
        ]);

        if (!empty($this->username) && !empty($this->password)) {
            $connectionArray = array_merge($connectionArray, [
                'username' => $this->username,
                'password' => $this->password,
                'host' => $this->host,
                'port' => $this->port,
            ]);
        }


        Config::set("database.connections.$connection", $connectionArray);

        try {
            // reconnect to database with new settings
            DB::reconnect();
            return DB::connection()->getPdo();
        } catch (\Throwable $th) {
            $this->addError('checkConnection', $th->getMessage());

            return false;
        }
    }

    public function migrate()
    {
        Artisan::call('optimize:clear');
        Artisan::call('storage:link');
        Artisan::call('migrate:fresh --force --seed');
    }

    public function process()
    {
        if (!$this->checkConnection()) return false;

        $this->migrate();
    }
}
