<?php

class TestCase extends Orchestra\Testbench\TestCase {

    protected function getPackageProviders()
    {
        return array('Tikamsah\OAuth2Server\OAuth2ServerServiceProvider');
    }

    protected function getPackageAliases()
    {
        return array(
            'AuthorizationServer' => 'Tikamsah\OAuth2Server\Facades\AuthorizationServerFacade',
            'ResourceServer'  => 'Tikamsah\OAuth2Server\Facades\ResourceServerFacade',
        );
    }

}
