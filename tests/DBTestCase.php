<?php

abstract class DBTestCase extends TestCase
{
    protected $artisan;
    //protected $files;

    /*protected function createMigrationsFiles()
    {
        if ( ! $this->files->exists(__DIR__.'/../tmp')) {

            $this->files->makeDirectory(__DIR__.'/../tmp');

            $this->artisan->call('oauth2-server:migrations', [
                '--path' => '../tmp'
            ]);
        }
    }

    protected function deleteMigrationsFiles()
    {
        $this->files->deleteDirectory(__DIR__.'/../tmp');
    }*/

    public function setUp()
    {
        parent::setUp();

        $this->artisan = $this->app->make('artisan');
        //$this->files = $this->app->make('files');

        //$this->createMigrationsFiles();

        $this->artisan->call('migrate', [
            '--database' => 'testbench',
            '--path' => '../migrations'
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

    /*public function __destruct()
    {
        $this->deleteMigrationsFiles();
    }*/
}
