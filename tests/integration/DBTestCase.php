<?php

abstract class DBTestCase extends TestCase
{
    protected $artisan;

    public function setUp()
    {
        parent::setUp();

        $this->artisan = $this->app->make('artisan');
        $this->artisan->call('migrate', [
            '--database' => 'testbench',
            '--path' => '../src/migrations'
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

    protected function getPackageProviders()
    {
        return [];
    }

    protected function getPackageAliases()
    {
        return [];
    }

    public function teardown()
    {
        $this->artisan->call('migrate:reset');
    }
}
