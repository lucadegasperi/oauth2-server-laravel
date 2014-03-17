<?php

class DBTestCase extends TestCase
{
    protected $artisan;
    protected $files;

    protected function createMigrations()
    {

    }

    public function setUp()
    {
        parent::setUp();

        $this->artisan = $this->app->make('artisan');
        $this->files = $this->app->make('files');

        if ( ! $this->files->exists(__DIR__.'/../tmp')) {

            var_dump('called');

            $this->files->makeDirectory(__DIR__.'/../tmp');

            $this->artisan->call('oauth2-server:migrations', [
                '--path' => '../tmp'
            ]);
        }

        $this->artisan->call('migrate', [
            '--database' => 'testbench',
            '--path' => '../tmp'
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

    public function teardown()
    {
        $this->artisan->call('migrate:reset');
    }

    public function __destruct()
    {
        $this->files->deleteDirectory(__DIR__.'/../tmp');
    }
}
