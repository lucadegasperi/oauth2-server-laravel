<?php namespace LucaDegasperi\OAuth2Server\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

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
        'create_oauth_clients_table'
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

        $path = $this->laravel['path'].'/database/migrations';

        foreach ($this->stubs as $stub) {
            $fullPaths[$stub] = $this->laravel['migration.creator']->create($stub, $path);
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
}
