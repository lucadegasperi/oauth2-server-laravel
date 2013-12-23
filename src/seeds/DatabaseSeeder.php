<?php

class DatabaseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment() === 'production') {
            exit('I just stopped you getting fired. Love Luca');
        }
        
        Eloquent::unguard();

        $this->call('ClientsTableSeeder');
        $this->call('GrantsTableSeeder');
        $this->call('ScopesTableSeeder');
        $this->call('SessionsTableSeeder');
    }

}