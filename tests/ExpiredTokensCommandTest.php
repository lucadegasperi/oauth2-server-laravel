<?php

use \Mockery as m;
use LucaDegasperi\OAuth2Server\Commands\ExpiredTokensCommand;
use Symfony\Component\Console\Tester\CommandTester;

class ExpiredTokensCommandTest extends TestCase
{
    public function getSession()
    {
        return m::mock('LucaDegasperi\OAuth2Server\Repositories\SessionManagementInterface');
    }

    public function getCommand($session)
    {
        return new ExpiredTokensCommand($session);
    }

    public function testFiresWithDeleteOption()
    {
        $session = $this->getSession();
        $session->shouldReceive('deleteExpired')->once()->andReturn(5);
        $comm = $this->getCommand($session);

        $tester = new CommandTester($comm);

        $tester->execute(array('--delete' => true));

        $this->assertEquals("5 expired OAuth tokens were deleted\n", $tester->getDisplay());
    }

    public function testDoesntFireWithoutDeleteOption()
    {
        $session = $this->getSession();

        $comm = $this->getCommand($session);
        
        $tester = new CommandTester($comm);

        $tester->execute(array('--delete' => false));

        $this->assertEquals(
            "use the --delete option to trigger the delete of the expired tokens\n",
            $tester->getDisplay()
        );
    }

    public function tearDown()
    {
        m::close();
    }
}
