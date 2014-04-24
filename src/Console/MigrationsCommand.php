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
     * Create a new reminder table command instance.
     *
     * @return \LucaDegasperi\OAuth2Server\Console\MigrationsCommand
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->call('migrate:publish', ['package' => 'lucadegasperi/oauth2-server-laravel']);
        $this->call('dump-autoload');
    }
}
