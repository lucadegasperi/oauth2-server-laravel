<?php

use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $app['path.base'] = __DIR__ . '/../src';
    }

    protected function getPackageProviders()
    {
        return array('LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider');
    }

    protected function getPackageAliases()
    {
        return array(
            'Authorizer' => 'LucaDegasperi\OAuth2Server\Facades\Authorizer',
        );
    }
}
