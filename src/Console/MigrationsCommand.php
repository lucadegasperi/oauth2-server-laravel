<?php namespace LucaDegasperi\OAuth2Server\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;

class MigrationsCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'oauth2-server:migrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the migrations needed for and OAuth 2 Server';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The stubs names
     *
     * @var array
     */
    protected $stubs = [
        'create_oauth_clients_table',
        'create_oauth_scopes_table',
        'create_oauth_grants_table',
        'create_oauth_sessions_table',
        'create_oauth_session_scopes_table',
        'create_oauth_client_endpoints_table',
        'create_oauth_client_scopes_table',
        'create_oauth_client_grants_table',
        'create_oauth_auth_codes_table',
        'create_oauth_auth_code_scopes_table',
        'create_oauth_access_tokens_table',
        'create_oauth_access_token_scopes_table',
        'create_oauth_refresh_tokens_table',
    ];

    /**
     * Create a new reminder table command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $fullPaths = $this->createBaseMigrations();

        foreach ($fullPaths as $stub => $fullPath) {
            $this->files->put($fullPath, $this->getMigrationStub($stub));
            $this->info('Migration '. $stub .' created');
        }

        $this->info('All Migrations created successfully!');

        $this->call('dump-autoload');
    }

    /**
     * Create a base migration file for the reminders.
     *
     * @return string
     */
    protected function createBaseMigrations()
    {
        $fullPaths = [];

        $path = $this->getMigrationsPath();

        foreach ($this->stubs as $stub) {
            $fullPaths[$stub] = $this->laravel['migration.creator']->create($stub, $path);
            sleep(1);
        }

        return $fullPaths;
    }

    /**
     * Get the stub's content
     * @param  string $stub the stub name
     * @return string       the stub's content
     */
    protected function getMigrationStub($stub)
    {
        return $this->files->get(__DIR__.'/../../stubs/migrations/'. $stub .'.stub');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['path', null, InputOption::VALUE_OPTIONAL, 'Where to store the migrations.', null],
        ];
    }

    /**
     * Get the migrations path
     * @return string the path where to store migrations
     */
    protected function getMigrationsPath()
    {
        $path = $this->input->getOption('path');

        if (! is_null($path)) {
            return $this->laravel['path.base'].'/'.$path;
        }

        return $this->laravel['path'].'/database/migrations';
    }
}
