<?php
/**
 * Command to ease the creation of a sample Authorization Server controller
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Console;

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
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @return \LucaDegasperi\OAuth2Server\Console\OAuthControllerCommand
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

            $this->comment("Routes:");
            $this->comment("Route::post('oauth/access_token', 'OAuthController@postAccessToken');");
        }
        else {
            $this->error('OAuth Server controller already exists!');
        }
    }
}
