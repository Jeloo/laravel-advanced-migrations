<?php

namespace Jeloo\LaraMigrations;

use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{

    public function register()
    {
        $this->registerCommand();
        $this->registerConfig();
        $this->registerMigrationNameParser();
        $this->registerSchemaParser();
        $this->registerGenerator();
    }

    public function registerCommand()
    {
        $this->app->singleton('command.make:migration@', function ($app) {
            return new MigrationCommand();
        });

        $this->commands('command.make:migration@');
    }

    public function registerConfig()
    {
        $this->app->bind('make:migration@.config', function () {
            return new Repository(require(__DIR__.'/meta.php'));
        });
    }

    public function registerMigrationNameParser()
    {
        $this->app->bind('make:migration@.nameParser', function ($app) {
            /** @var MigrationCommand $command */
            $command = $app['command.make:migration@'];
            return new MigrationNameParser($command->getMigrationName());
        });
    }

    public function registerSchemaParser()
    {
        $this->app->bind(SchemaParserInterface::class, function () {
            return new SchemaParser();
        });
    }

    public function registerGenerator()
    {
        $this->app->bind(AbstractGenerator::class, function () {
            return new MetaBasedGenerator(
                [],
                []
            );
        });
    }

}