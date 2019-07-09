<?php

namespace BajakLautMalaka\PmiDonatur\Tests\Unit;

use Tests\TestCase;
use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Foundation\Testing\WithFaker;

use BajakLautMalaka\PmiAdmin\Admin;
use BajakLautMalaka\PmiDonatur\Donation;

class DonationUnitTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function testPostDonationWithoutData()
    {
        $response = $this->postJson('/api/app/donations/create');
        $response->assertStatus(422);
    }

    /** @test */
    public function testGetDonationListFromApp()
    {
        $campaignId = \BajakLautMalaka\PmiDonatur\Campaign::all()->random(1)->first()->id;
        $response   = $this->getJson('/api/app/donations/list/'. $campaignId);
        $response->assertStatus(200);
    }

    /** @test */
    public function testGetDonationListFromAdminUnauthenticated()
    {
        $campaignId = \BajakLautMalaka\PmiDonatur\Campaign::all()->random(1)->first()->id;
        $response   = $this->getJson('/api/admin/donations/list/'. $campaignId);
        $response->assertStatus(401);
    }

    /** @test */
    public function testGetDonationListFromAdmin()
    {
        $admin      = factory(Admin::class)->create();
        $campaignId = \BajakLautMalaka\PmiDonatur\Campaign::all()->random(1)->first()->id;
        $response   = $this->actingAs($admin)
                        ->getJson('/api/admin/donations/list/'. $campaignId);
        $response->assertStatus(200);
    }

    /** @test */
    public function testPostDonationWithProperData()
    {
        $response = $this->postJson('/api/app/donations/create', [
            'campaign_id' => 1,
            'category'    => 1,
            'name'        => 'Test Donatur 1',
            'email'       => 'test1@donatur.com',
            'phone'       => $this->faker->shuffle('0123456789'),
            'amount'      => 5000000
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success'
            ]);
    }

    /** @test */
    public function testDonationProofUpload()
    {
        $donation = factory(Donation::class)->create();
        $image    = UploadedFile::fake()->image('donation_proof.jpg', 1, 1);
        $response = $this->postJson('/api/app/donations/proof-upload', [
            'id'    => $donation->id,
            'image' => $image
        ]);
        $response->assertStatus(200);
    }

    /** @test */
    public function testUpdateDonationStatus()
    {
        $admin      = factory(\BajakLautMalaka\PmiAdmin\Admin::class)->create();
        $donationId = \BajakLautMalaka\PmiDonatur\Donation::all()->random(1)->first()->id;
        $response   = $this->actingAs($admin)
                        ->postJson('/api/admin/donations/update-status/'.$donationId, [
                            'status' => 3
                        ]);
        $response->assertStatus(200);
    }
}