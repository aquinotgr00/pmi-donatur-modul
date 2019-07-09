<?php

namespace BajakLautMalaka\PmiDonatur\Tests\Unit;

use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use BajakLautMalaka\PmiAdmin\Admin;

class CampaignUnitTest extends Orchestra
{
    use WithFaker, RefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        //$this->artisan('migrate', ['--database' => 'testing']);

        $this->loadMigrationsFrom(realpath(__DIR__ . '/../../database/migrations'));
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    /** @test */
    public function it_runs_the_migrations()
    {
        $admin = factory(Admin::class)->create();
        $token = $admin->createToken('TestToken', [])->accessToken;
        $response = $this->json('GET', '/api/app/campaigns', [
            'Accept' => 'application/json',
            'authorization' => "Bearer $token",
        ]);
        $response->assertStatus(200);
    }
}
