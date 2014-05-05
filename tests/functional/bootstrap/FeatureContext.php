<?php

use Behat\Behat\Context\ClosuredContextInterface;
use Behat\Behat\Context\TranslatedContextInterface;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use LucaDegasperi\LaravelFeatureContext\LaravelFeatureContext;

/**
 * Features context.
 */
class FeatureContext extends LaravelFeatureContext
{

    /**
     * Get package aliases.
     *
     * @return array
     */
    protected function getPackageAliases()
    {
        return [
            'Authorizer' => 'LucaDegasperi\OAuth2Server\Facades\Authorizer',
        ];
    }

    /**
     * Get package providers.
     *
     * @return array
     */
    protected function getPackageProviders()
    {
        return ['LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider'];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['path.base'] = __DIR__ . '/../../../src';
    }
}
