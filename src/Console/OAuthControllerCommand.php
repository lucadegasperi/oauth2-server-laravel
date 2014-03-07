<?php namespace LucaDegasperi\OAuth2Server\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class OAuthControllerCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'oauth2-server:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a stub OAuth 2.0 server controller';

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
        $destination = $this->laravel['path'].'/controllers/OAuthController.php';

        if (!$this->files->exists($destination)) {
            $this->files->copy(__DIR__.'/../../stubs/controller.stub', $destination);

            $this->info('OAuth Server controller controller created successfully!');

            $this->comment("Route: Route::controller('oauth', 'OAuthController');");
        }
        else {
            $this->error('OAuth Server controller already exists!');
        }
    }
}
