<?php

namespace BajakLautMalaka\PmiDonatur\Tests\Unit;

use Tests\TestCase;

use Illuminate\Foundation\Testing\WithFaker;

class DonationUnitTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function testPostDonationWithoutData()
    {
        $response = $this->postJson('/api/donations/create');
        $response->assertStatus(422);
    }

    public function testPostDonationWithProperData()
    {
        $response = $this->postJson('/api/donations/create', [
            'campaign_id' => 1,
            'category'    => 1,
            'name'        => 'Test Donatur 1',
            'email'       => 'test1@donatur.com',
            'phone'       => $this->faker->shuffle('0123456789'),
            'amount'      => 5000000
        ]);

        dd($response->getContent());

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success'
            ]);
    }
}