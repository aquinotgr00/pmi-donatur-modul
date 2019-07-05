<?php

namespace BajakLautMalaka\PmiDonatur\Tests\Unit;

use Tests\TestCase;
use App\User;
use BajakLautMalaka\PmiDonatur\Donator;
// use BajakLautMalaka\PmiDonatur\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DonatorUnitTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function testPostSignupWithoutData()
    {
        $response = $this->json('POST', '/api/donators/signup');
        $response->assertStatus(422);
    }

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
            'image' => 'donator_image.jpg',
            'name' => $this->faker->unique()->name,
            'email' => $this->faker->unique()->email,
            'phone' => $this->faker->shuffle('0123456789'),
            'password' => 'open1234',
            'password_confirmation' => 'open1234',
            'url_action' => 'dummy.frontend'
        ]);
        
        $response
                ->assertStatus(200)
                ->assertJson([
                    'access_token' => json_decode($response->getContent())->access_token,
                ]);
    }

    /** @test */
    public function testSigninWithIncorectCredentials()
    {
        $this->postJson('/api/donators/signin', [
            'email' => 'somerandom@mail.com',
            'password' => 'open1234'
        ])->assertStatus(401)
        ->assertJson([
            'message' => 'Account does not exist'
        ]);
    }

    /** @test */
    public function testSigninWithCorrectCredentials()
    {
        $email = $this->faker->unique()->email;
        factory(User::class)->create([
            'email' => $email,
            'password' => bcrypt('open1234')
        ]);

        $response = $this->postJson('/api/donators/signin', [
            'email' => $email,
            'password' => 'open1234'
        ]);
        $response->assertStatus(200)
        ->assertJson([
            'access_token' => json_decode($response->getContent())->access_token,
        ]);
    }

    /** @test */
    public function testRequestForgotPasswordTokenWithIncorectEmail()
    {
        $this->postJson('/api/donators/password/reset', [
            'email' => 'randomE@mail.com',
            'url_action' => 'testing'
        ])->assertJson([
            'message' => 'Email not found'
        ]);
    }

    /** @test */
    public function testRequestForgotPasswordTokenWithCorrectEmail()
    {
        $email = $this->faker->unique()->email;
        factory(User::class)->create([
            'email' => $email,
            'password' => bcrypt('open1234')
        ]);
        $response = $this->postJson('/api/donators/password/reset', [
            'email' => $email,
            'url_action' => 'testing'
        ]);
        
        $response->assertJson([
            'reset_password_token' => json_decode($response->getContent())->reset_password_token
        ]);
    }

    /** @test */
    public function testChangePassword()
    {
        $email = $this->faker->unique()->email;
        factory(User::class)->create([
            'email' => $email,
            'password' => bcrypt('open1234')
        ]);
        $response = $this->postJson('/api/donators/password/reset', [
            'email' => $email,
            'url_action' => 'testing'
        ]);
        $token = json_decode($response->getContent())->reset_password_token;
        $passwordChanged = $this->postJson('/api/donators/password/change', [
            'token' => $token,
            'password' => 'open1234',
            'password_confirmation' => 'open1234'
        ]);
        
        $passwordChanged->assertJson([
            'access_token' => json_decode($passwordChanged->getContent())->access_token
        ]);
    }

    public function testUpdateDonatorProfile()
    {
        $donator = Donator::all()->random(1)->first();
        $this->postJson('/api/donators/update-profile/'.$donator->id, [
            'dob'         => '12/01/2001',
            'address'     => 'jakarta',
            'province'    => 'jakarta',
            'city'        => 'jakarta',
            'subdistrict' => 'jakarta',
            'subdivision' => 'kampung betawi',
            'postal_code' => '12342',
            'gender'      => 'male'
        ])->assertJson([
            'message' => 'Your data sucessfully changed.'
        ]);
    }
}
