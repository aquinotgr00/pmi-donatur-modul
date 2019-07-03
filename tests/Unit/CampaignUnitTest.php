<?php

namespace BajakLautMalaka\PmiDonatur\Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;

class CampaignUnitTest extends TestCase
{
    use WithFaker;

    public function testListCampaign()
    {
        $response = $this->json('GET', '/api/campaigns');
        $response->assertStatus(200);
        $response->assertSee('data');
    }

    public function testCreateCampaignWithoutUserID()
    {
        $data = [
            'image_file' => '',
            'type_id' => 1,
            'title' => $this->faker->unique()->name,
            'description' => 'lorem ipsum',
            'amount_goal' => 10000
        ];

        $response = $this->json('POST', '/api/campaign', $data);
        $response->assertStatus(200);
        $response->assertSee('user_id');
    }

    public function testCreateCampaignCompleted()
    {
        Storage::fake('public');
        $this->postJson('api/campaign', [
            'image_file' => $file = UploadedFile::fake()->image('image.jpg', 1, 1),
            'user_id' => 1,
            'type_id' => 1,
            'title' => $this->faker->unique()->name,
            'description' => 'lorem ipsum',
            'amount_goal' => 10000,
        ])->assertStatus(200)
            ->assertSee('image');
    }

    public function testUpdateCampaign()
    {
        $this->postJson('api/campaigns/1', [
            'title' => 'cek update',
            'description' => 'lorem ipsum',
            'amount_goal' => 10000,
            '_method' => 'PUT'
        ])->assertStatus(200)
            ->assertSee('title');
    }

    public function testDetailsCampaign()
    {
        $this->getJson('api/campaigns/1')->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user_id',
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
