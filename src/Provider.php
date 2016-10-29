<?php

namespace Jeloo\LaraMigrations;

use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Builder as SchemaBuilder;

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
        $this->app->singleton('command.make:migration@', function () {
            return new MigrationCommand();
        });

        $this->commands('command.make:migration@');
    }

    public function registerConfig()
    {
        $this->app->bind('make:migration@.meta', function () {
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
        $this->app->bind('command.make:migration@.schemaParser', function ($app) {
            /** @var MigrationCommand $command */
            $command = $app['command.make:migration@'];
            return new SchemaParser($command->prepareSchema(), $app[SchemaBuilder::class]);
        });
    }

    public function registerGenerator()
    {
        $this->app->bind(AbstractGenerator::class, function ($app) {
            /** @var SchemaParserInterface $schemaParser */
            $schemaParser = $app['command.make:migration@.schemaParser'];
            /** @var Repository $meta */
            $meta = $app['make:migration@.meta'];
            /** @var MigrationCommand $command */
            $command = $app['command.make:migration@'];

            $meta = new Repository($meta->get($command->getMigrationVerb()));

            return new MetaBasedGenerator(
                $schemaParser->parse(),
                $meta
            );
        });

        $this->app->alias(AbstractGenerator::class, 'make:migration@.generator');
    }

}