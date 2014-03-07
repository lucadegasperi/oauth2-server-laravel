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
        $fullPath = $this->createBaseMigration();

        $this->files->put($fullPath, $this->getMigrationStub());

        $this->info('Migrations created successfully!');

        $this->call('dump-autoload');
    }

    /**
     * Create a base migration file for the reminders.
     *
     * @return string
     */
    /*protected function createBaseMigration()
    {
        $name = 'create_password_reminders_table';

        $path = $this->laravel['path'].'/database/migrations';

        return $this->laravel['migration.creator']->create($name, $path);
    }

    /**
     * Get the contents of the reminder migration stub.
     *
     * @return string
     */
    /*protected function getMigrationStub()
    {
        $stub = $this->files->get(__DIR__.'/../../stubs/reminders.stub');

        return str_replace('password_reminders', $this->getTable(), $stub);
    }

    /**
     * Get the password reminder table name.
     *
     * @return string
     */
    /*protected function getTable()
    {
        return $this->laravel['config']->get('auth.reminder.table');
    }*/
}
