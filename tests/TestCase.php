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
        $app['path.base'] = __DIR__ . '/../src';
    }

    protected function getPackageProviders()
    {
        return [
            'LucaDegasperi\OAuth2Server\Storage\FluentStorageServiceProvider',
            'LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider'
        ];
    }

    protected function getPackageAliases()
    {
        return [
            'Authorizer' => 'LucaDegasperi\OAuth2Server\Facades\Authorizer',
        ];
    }
}
