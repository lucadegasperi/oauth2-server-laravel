<?php

abstract class DBTestCase extends TestCase
{
    protected $artisan;

    public function setUp()
    {
        parent::setUp();

        $this->artisan = $this->app->make('Illuminate\Contracts\Console\Kernel');
        $this->artisan->call('migrate', [
            '--database' => 'testbench',
            '--path' => '../../../../migrations'
        ]);
        $this->artisan->call('db:seed');
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => ''
        ]);
    }

    public function tearDown()
    {
        //$this->artisan->call('migrate:reset');
    }
}
