<?php

namespace BajakLautMalaka\PmiDonatur\Tests\Unit;

use BajakLautMalaka\PmiDonatur\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Storage;
use BajakLautMalaka\PmiDonatur\Campaign;

class CampaignUnitTest extends TestCase
{
    use WithFaker;
    use WithoutMiddleware;

    public function testListCampaign()
    {
        $response = $this->json('GET', '/api/app/campaigns');
        $response->assertStatus(200);
        $response->assertSee('data');
    }

    public function testCreateCampaignWithoutImage()
    {
        $data = [
            'image_file' => '',
            'type_id' => 1,
            'title' => $this->faker->unique()->name,
            'description' => 'lorem ipsum',
            'amount_goal' => 10000,
            'publish'=> 1,
        ];

        $response = $this->json('POST', '/api/admin/campaign', $data);
        $response->assertStatus(200);
        $response->assertSee('image_file');
    }

    public function testCreateCampaignCompleted()
    {
        Storage::fake('public');
        $this->postJson('api/admin/campaign', [
            'image_file' => $file = UploadedFile::fake()->image('image.jpg', 1, 1),
            'fundraising' => 1,
            'type_id' => 1,
            'title' => $this->faker->unique()->name,
            'description' => 'lorem ipsum',
            'amount_goal' => 10000,
            'publish'=> 1,
        ])->assertStatus(200)
            ->assertSee('image');
    }

    public function testUpdateCampaign()
    {
        $campaign = Campaign::create([
            'image' => 'http://google.com',
            'type_id' => 1,
            'admin_id' => 1,
            'title' => $this->faker->unique()->name,
            'description' => 'lorem ipsum',
            'amount_goal' => 10000,
            'publish'=> 1,
        ]);
        $this->postJson('api/admin/campaigns/'.$campaign->id, [
            'title' => 'cek update',
            'description' => 'lorem ipsum',
            'amount_goal' => 10000,
            '_method' => 'PUT'
        ])->assertStatus(200)
            ->assertSee('title');
    }

    public function testDetailsCampaign()
    {
        $campaign = Campaign::create([
            'image' => 'http://google.com',
            'type_id' => 1,
            'admin_id' => 1,
            'title' => $this->faker->unique()->name,
            'description' => 'lorem ipsum',
            'amount_goal' => 10000,
            'publish'=> 1,
        ]);
        $this->getJson('api/app/campaigns/'.$campaign->id)->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'fundraising',
                    'type_id',
                    'title',
                    'image',
                    'description',
                    'amount_goal',
                    'amount_real',
                    'start_campaign',
                    'finish_campaign',
                    'fundraising',
                    'publish',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'amount_donation',
                    'get_type' => [
                        'id',
                        'name',
                        'description',
                        'created_at',
                        'updated_at'
                    ],
                    'get_donations' => []

                ]
            ]);
    }
}
