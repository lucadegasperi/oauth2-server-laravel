<?php namespace LucaDegasperi\OAuth2Server\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

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

	/**
	 * Create a new command instance.
	 *
	 * @return void
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
		//
		var_dump('fails');
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

}