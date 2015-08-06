<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class AbstractTestCase extends OrchestraTestCase
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
        return __DIR__.'/../vendor/orchestra/testbench/fixture';
    }

    protected function getPackageProviders($app)
    {
        return [
            'LucaDegasperi\OAuth2Server\Storage\FluentStorageServiceProvider',
            'LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider',
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Authorizer' => 'LucaDegasperi\OAuth2Server\Facades\Authorizer',
        ];
    }
}
