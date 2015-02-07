<?php

use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Get base path.
     *
     * @return string
     */
    protected function getBasePath()
    {
        // reset base path to point to our package's src directory
        return __DIR__ . '/../vendor/orchestra/testbench/fixture';
    }

    protected function getPackageProviders($app)
    {
        return [
            'LucaDegasperi\OAuth2Server\Storage\FluentStorageServiceProvider',
            'LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider'
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Authorizer' => 'LucaDegasperi\OAuth2Server\Facades\Authorizer',
        ];
    }
}
