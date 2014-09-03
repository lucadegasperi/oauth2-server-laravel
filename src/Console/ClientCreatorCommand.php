<?php
/**
 * Command to ease the creation of an OAuth Client
 *
 * @package   lucadegasperi/oauth2-server-laravel
 * @author    Luca Degasperi <luca@lucadegasperi.com>
 * @copyright Copyright (c) Luca Degasperi
 * @licence   http://mit-license.org/
 * @link      https://github.com/lucadegasperi/oauth2-server-laravel
 */

namespace LucaDegasperi\OAuth2Server\Console;

use Illuminate\Console\Command;
use LucaDegasperi\OAuth2Server\Storage\FluentClient;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Str;

class ClientCreatorCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'oauth2-server:client:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new OAuth Client';

    /**
     * @var FluentClient
     */
    protected $clientRepo;

    /**
     * @param FluentClient $clientRepo
     */
    public function __construct(FluentClient $clientRepo)
    {
        parent::__construct();
        $this->clientRepo = $clientRepo;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $clientName = $this->argument('clientName');
        $clientId = $this->argument('clientId');
        $clientSecret = $this->argument('clientSecret');

        try {
            $this->clientRepo->create($clientId, $clientName, $clientSecret);
            $this->info('Client created successfully');
            $this->info('Client Name: '.$clientName);
            $this->info('Client ID: '.$clientId);
            $this->info('Client Secret: '.$clientSecret);
        } catch(\Exception $e) {
            $this->error('A client with the same Name, Id or Secret already exists. Try again.');
        }
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return [
            ['clientName', InputArgument::REQUIRED, 'The Client\'s name'],
            ['clientId', InputArgument::OPTIONAL, 'Client ID to use. A random one will be generated if none is provided.', Str::random()],
            ['clientSecret', InputArgument::OPTIONAL, 'Client Secret to use. A random one will be generated if none is provided.', Str::random(32)]
        ];
    }
}
