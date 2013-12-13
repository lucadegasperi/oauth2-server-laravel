<?php

use Mockery as m;
use LucaDegasperi\OAuth2Server\Repositories\FluentClient;

class FluentClientTest extends TestCase
{
    protected $artisan;

    public function setUpDb()
    {
        $this->artisan = $this->app->make('artisan');


        $this->artisan->call('migrate',  array(
            '--database' => 'testbench',
            '--path' => 'migrations',
            
        ));
        //Artisan::call('db:seed');
    }

    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $app['path.base'] = __DIR__ . '/../src';

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', array(
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ));
    }

    public function teardownDb()
    {
        $this->artisan->call('migrate:reset');
    }

    public function setUp()
    {
        parent::setUp();

        $this->setUpDb();
        
    }

    public function teardown()
    {
        $this->teardownDb();
        m::close();
    }

    public function test_nothing()
    {
        $repo = new FluentClient();
        $result = $repo->getClient("foo", "bar");

        var_dump($result);
    }
}