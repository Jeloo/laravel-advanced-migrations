<?php

namespace Jeloo\LaraMigrations;

use Orchestra\Testbench\TestCase;
use Artisan;

class MigrationCommandTestCase extends TestCase
{

    public function testRegistersObjects()
    {
        Artisan::call('make:migration@', [
            'name' => 'create_users',
            'columns' => 'id,email:nullable:unique'
        ]);

        $output = Artisan::output();
    }

    public function getPackageProviders($app)
    {
        return [
            Provider::class
        ];
    }

}