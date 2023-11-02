<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DbCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $schemaName = $this->argument('name');
        $charset = config('database.connections.mysql.charset', 'utf8mb4');
        $collation = config('database.connections.mysql.collation', 'utf8mb4_0900_ai_ci');
        // $collation = 'utf8mb4_0900_ai_ci';

        // Check if the database already exists
        if ($this->databaseExists($schemaName)) {
            $this->info("Database '$schemaName' already exists!");
        } else {
            $this->createDatabase($schemaName, $charset, $collation);
            $this->info("Database '$schemaName' successfully created!");
        }
    }

    private function databaseExists($schemaName)
    {
        $result = DB::select("SHOW DATABASES LIKE '$schemaName'");

        return count($result) > 0;
    }

    private function createDatabase($schemaName, $charset, $collation)
    {
        // Temporarily set the database name to null in the config
        config(['database.connections.mysql.database' => null]);

        $query = "CREATE DATABASE IF NOT EXISTS $schemaName CHARACTER SET $charset COLLATE $collation;";
        DB::statement($query);

        // Restore the original database name in the config
        config(['database.connections.mysql.database' => $schemaName]);
    }
}
