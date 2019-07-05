<?php

namespace BajakLautMalaka\PmiDonatur\Tests\Unit;

use BajakLautMalaka\PmiDonatur\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DonatorUnitTest extends TestCase
{
    use WithFaker;
    /**
     *  Test donator sign up
     *
     * @return void
     */
    public function testSignupDonatorWithProperData()
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->image('donator_image.jpg', 1, 1);
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->postJson('/api/donators/signup', [
            'image' => $image,
            'name' => $this->faker->unique()->name,
            'email' => $this->faker->unique()->email,
            'phone' => $this->faker->randomNumber,
            'password' => 'open1234',
            'password_confirmation' => 'open1234',
            'url_action' => 'dummy.frontend'
        ]);
        
        $response->assertStatus(200);
    }
}
