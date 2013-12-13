<?php

class TestCase extends Orchestra\Testbench\TestCase
{

    protected $artisan;

    protected function setUpDb()
    {
        $this->artisan = $this->app->make('artisan');


        $this->artisan->call('migrate',  array(
            '--database' => 'testbench',
            '--path' => 'migrations',
            
        ));
        $this->artisan->call('db:seed');
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

    protected function teardownDb()
    {
        $this->artisan->call('migrate:reset');
    }

    protected function getPackageProviders()
    {
        return array('LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider');
    }

    protected function getPackageAliases()
    {
        return array(
            'AuthorizationServer' => 'LucaDegasperi\OAuth2Server\Facades\AuthorizationServerFacade',
            'ResourceServer'  => 'LucaDegasperi\OAuth2Server\Facades\ResourceServerFacade',
        );
    }

}
