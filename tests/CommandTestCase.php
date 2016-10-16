<?php

namespace Jeloo\LaraMigrations;

use Illuminate\Contracts\Console\Kernel;
//use Illuminate\Foundation\Testing\TestCase;
use Orchestra\Testbench\TestCase;

class CommandTestCase extends TestCase
{

    /**
     * Boots the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = $this-app;
        dd($app);

        $app->register(Provider::class);

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}