<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use LucaDegasperi\OAuth2Server\Tests\Database\Seeders\OAuth2DatabaseSeeder;

abstract class AbstractDBTestCase extends AbstractTestCase
{
    protected $artisan;

    public function setUp()
    {
        parent::setUp();

        $this->artisan = $this->app->make('Illuminate\Contracts\Console\Kernel');
        $this->artisan->call('migrate', [
            '--database' => 'testbench',
            '--path' => '../../../../database/migrations',
        ]);
        $this->artisan->call('db:seed', [
            '--class' => OAuth2DatabaseSeeder::class,
        ]);
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    public function tearDown()
    {
        //$this->artisan->call('migrate:reset');
    }
}
