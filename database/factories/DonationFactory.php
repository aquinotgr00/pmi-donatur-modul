<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use BajakLautMalaka\PmiAdmin\Admin;
use BajakLautMalaka\PmiDonatur\Donation;
use Faker\Generator as Faker;

$factory->define(Donation::class, function (Faker $faker) {
        
    $number_of_active_admins = Admin::active()->count();

    return [
        'name'=> $faker->name,
        'email'=> $faker->unique()->email,
        'phone'=> $faker->phoneNumber,
        'image'=> $faker->imageUrl(640,480),
        'category'=> $faker->numberBetween(1,4),
        'amount'=> $faker->numberBetween(1000000,25000000),
        'pick_method'=> $faker->randomElement([1,2]),
        'payment_method'=> $faker->randomElement([1,2]),
        'status'=> $faker->numberBetween(1,4),
        'guest'=> $faker->boolean,
        'anonym'=> $faker->boolean,
        'admin_id'=> $faker->numberBetween(2,$number_of_active_admins),
        'donator_id'=> 1,//\BajakLautMalaka\PmiDonatur\Donator::all()->unique()->random()->id,
        'campaign_id'=> \BajakLautMalaka\PmiDonatur\Campaign::all()->unique()->random()->id
    ];
});
