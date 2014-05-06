<?php

use Behat\Behat\Context\ClosuredContextInterface;
use Behat\Behat\Context\TranslatedContextInterface;
use Behat\Behat\Event\FeatureEvent;
use Behat\Behat\Event\ScenarioEvent;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use LucaDegasperi\LaravelFeatureContext\LaravelFeatureContext;

/**
 * Features context.
 */
class FeatureContext extends LaravelFeatureContext
{
    protected $artisan;

    /** @BeforeScenario */
    public function setup()
    {
        $this->migrateAndSeed();
    }

    /** @AfterScenario */
    public function teardown()
    {
        $this->artisan->call('migrate:reset');
    }
    /**
     * @Given /^An authorization server exists that supports the "([^"]*)" grant type$/
     */
    public function anAuthorizationServerExistsThatSupportsTheGrantType($arg1)
    {
        $clientCredentialsGrant = new ClientCredentialsGrant();
        Authorizer::getIssuer()->addGrantType($clientCredentialsGrant);

        $this->app['router']->enableFilters();
        $this->app['router']->post('oauth/access_token', 'OAuthController@postAccessToken');
    }

    /**
     * @Given /^I have invalid client credentials$/
     */
    public function iHaveInvalidClientCredentials()
    {
        //throw new PendingException();
    }

    /**
     * @When /^I post to the "([^"]*)" page "([^"]*)" "([^"]*)" "([^"]*)"$/
     */
    public function iPostToThePage($pageName, $grantType, $clientId, $clientSecret)
    {
        $params = [
            'grant_type' => $grantType,
            'client_id' => $clientId,
            'client_secret' => $clientSecret
        ];
        //$this->app['request']->replace($params);
        Authorizer::getIssuer()->getRequest()->request->add($params);
        $response = $this->call('POST', $pageName, $params);

        var_dump($response);
    }

    /**
     * @Then /^I should get an "([^"]*)" error$/
     */
    public function iShouldGetAnError($arg1)
    {
        $this->assertResponseStatus(401);
        //throw new PendingException();
    }
    /**
     * Get package aliases.
     *
     * @return array
     */
    protected function getPackageAliases()
    {
        return [
            'Authorizer' => 'LucaDegasperi\OAuth2Server\Facades\AuthorizerFacade',
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
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => ''
        ]);
        $this->artisan = $app->make('artisan');
    }

    public function migrateAndSeed()
    {
        $this->artisan->call('migrate', [
            '--database' => 'testbench',
            '--path' => '../src/migrations'
        ]);
        $this->artisan->call('db:seed');
    }
}
