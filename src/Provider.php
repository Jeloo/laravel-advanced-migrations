<?php

namespace Jeloo\LaraMigrations;

use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{

    public function register()
    {
        $this->registerCommand();
        $this->registerSchemaParser();
        $this->registerGenerator();
    }

    public function registerCommand()
    {
        $this->app->singleton('command.make:migration@', function () {
            return new MigrationCommand();
        });

        $this->commands('command.make:migration@');
    }

    public function registerSchemaParser()
    {
        $this->app->bind(SchemaParserInterface::class, function () {
            return new SchemaParser();
        });
    }

    public function registerGenerator()
    {
        $this->app->bind(AbstractGenerator::class, function ($app) {
            return new MetaBasedGenerator(
                [],
                []
            );
        });
    }

}