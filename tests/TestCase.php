<?php

namespace Messerli90\Teamwork\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as TestBenchCase;
use Messerli90\Teamwork\TeamworkServiceProvider;

class TestCase extends TestBenchCase
{
    protected function getPackageProviders($app)
    {
        return [TeamworkServiceProvider::class];
    }

    // protected function getPackageAliases($app)
    // {
    //     return [
    //         'Teamwork' => 'Messerli90\Teamwork\Facades\Teamwork'
    //     ];
    // }

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->createUsersTable();
        $this->createTeamsTable();

        $this->artisan('migrate', [
            '--database' => 'testbench',
            '--realpath' => realpath(__DIR__ . '/../database/migrations'),
        ]);

        $this->withFactories(__DIR__ . '/factories');
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('teamwork.user_model', \Messerli90\Teamwork\Tests\Models\User::class);
        $app['config']->set('teamwork.team_model', \Messerli90\Teamwork\Tests\Models\Team::class);
    }

    protected function createUsersTable()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamps();
        });
    }

    protected function createTeamsTable()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->unsignedInteger('owner_id');
            $table->string('name');
            $table->timestamps();
        });
    }
}
