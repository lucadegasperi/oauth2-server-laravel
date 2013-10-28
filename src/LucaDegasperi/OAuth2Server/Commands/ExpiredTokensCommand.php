<?php namespace LucaDegasperi\OAuth2Server\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use LucaDegasperi\OAuth2Server\Repositories\SessionManagementInterface;

class ExpiredTokensCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'oauth:expired-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A command to delete the OAuth expired tokens';


    protected $sessions;

    /**
     * Create a new command instance.
     *
     * @param SessionManagementInterface $sessions an implementation of the session management interface
     * @return void
     */
    public function __construct(SessionManagementInterface $sessions)
    {
        parent::__construct();

        $this->sessions = $sessions;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $value = $this->option('delete');
        if ($value === true) {
            $result = $this->deleteExpiredTokens();
            $this->info($result . ' expired OAuth tokens were deleted');
        } else {
            $this->info('use the --delete option to trigger the delete of the expired tokens');
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('delete', null, InputOption::VALUE_NONE, 'Effectively delete the tokens', null),
        );
    }

    /**
     * Deletes the sessions with expired authorization and refresh tokens from the db
     * 
     * @return int the number of sessions deleted
     */
    protected function deleteExpiredTokens()
    {
        return $this->sessions->deleteExpired();
    }
}
