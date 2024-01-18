<?php

namespace App\Utils;

use App\Actions\Conferences\ConferenceCreateAction;
use App\Actions\User\UserCreateAction;
use App\Events\AppInstalled;
use App\Models\Conference;
use App\Models\Enums\UserRole;
use App\Models\User;
use App\Models\Version;
use Illuminate\Encryption\Encrypter;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Installer
{
    public function __construct(
        public array $params,
    ) {
    }

    public function run()
    {
        try {
            $this->generateEnvFile();
            $this->migrate();
            $this->createConference();
            $this->createAccount();
    
            Version::application();
            
            AppInstalled::dispatch();
        } catch (\Throwable $th) {
            // backup and delete .env file
            $filesystem = app(Filesystem::class);
            $filesystem->copy(base_path('.env'), base_path('.env.backup'));
            $filesystem->delete(base_path('.env'));

            throw $th;
        }
    }

    public function migrate()
    {
        Artisan::call('optimize:clear');
        Artisan::call('storage:link');
        Artisan::call('icon:cache');

        Schema::dropAllTables();
        Artisan::call('migrate:fresh --force --seed');
    }

    public function generateEnvFile()
    {
        $envs = [
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'APP_URL' => url('/'),
            'APP_KEY' => 'base64:'.base64_encode(Encrypter::generateKey(config('app.cipher'))),
            'APP_TIMEZONE' => $this->readParam('timezone'),
            'DB_CONNECTION' => $this->readParam('db_connection'),
            'DB_HOST' => $this->readParam('db_host'),
            'DB_PORT' => $this->readParam('db_port'),
            'DB_DATABASE' => $this->readParam('db_name'),
            'DB_USERNAME' => $this->readParam('db_username'),
            'DB_PASSWORD' => $this->readParam('db_password'),
        ];

        $this->copyStubToPath('env', base_path('.env'), $envs);

    }

    /**
     * @param  array<string, string>  $replacements
     */
    function copyStubToPath(string $stub, string $targetPath, array $replacements = []): void
    {
        $filesystem = app(Filesystem::class);

        $stubPath = base_path("stubs" . DIRECTORY_SEPARATOR . "{$stub}.stub");

        $stub = str($filesystem->get($stubPath));

        foreach ($replacements as $key => $replacement) {
            $stub = $stub->replace("{{ {$key} }}", $replacement);
        }

        $stub = (string) $stub;

        $this->writeFile($targetPath, $stub);
    }

    protected function writeFile(string $path, string $contents): void
    {
        $filesystem = app(Filesystem::class); 

        $filesystem->ensureDirectoryExists(
            pathinfo($path, PATHINFO_DIRNAME),
        );

        $filesystem->put($path, $contents);
    }

    public function readParam($key)
    {
        return $this->params[$key] ?? null;
    }

    public function createConference(): Conference
    {
        return ConferenceCreateAction::run([
            'name' => $this->readParam('conference_name'),
            'type' => $this->readParam('conference_type'),
            'active' => true,
            'meta' => [
                'description' => $this->readParam('conference_description'),
            ],
        ]);
    }

    public function createAccount(): User
    {
        try {
            DB::beginTransaction();

            $user = UserCreateAction::run([
                'given_name' => $this->readParam('given_name'),
                'family_name' => $this->readParam('family_name'),
                'email' => $this->readParam('email'),
                'password' => $this->readParam('password'),
            ]);

            $user->assignRole(UserRole::Admin->value);

            DB::commit();
        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }

        return $user;
    }
}
