<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests;

use Illuminate\Bus\BusServiceProvider;
use Spatie\LaravelData\LaravelDataServiceProvider;
use Spatie\LaravelRay\RayServiceProvider;

/**
 * @internal
 */
abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * {@inheritDoc}
     */
    #[\Override()]
    protected function getEnvironmentSetUp($app): void
    {
        $app->config->set('app.key', 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');

        $app->config->set('cache.driver', 'array');

        $app->config->set('database.default', 'sqlite');
        $app->config->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app->config->set('mail.driver', 'log');

        $app->config->set('session.driver', 'array');

        $app->useStoragePath(\realpath(__DIR__.'/storage'));
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Application                                       $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    #[\Override()]
    protected function getPackageProviders($app)
    {
        return [
            BusServiceProvider::class,
            RayServiceProvider::class,
            LaravelDataServiceProvider::class,
        ];
    }
}
