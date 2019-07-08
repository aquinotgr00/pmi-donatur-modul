<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use BajakLautMalaka\PmiAdmin\Admin;
use BajakLautMalaka\PmiDonatur\Campaign;
use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(Campaign::class, function (Faker $faker) {
    $campaign_type = $faker->numberBetween(1,3);
    $fundraising = $faker->boolean;
    if($campaign_type===3) {
        $fundraising = true;
    }
    $amount_goal = $faker->optional->biasedNumberBetween(1000000,25000000);
    $amount_real = null;
    if($amount_goal) {
        $amount_real = $faker->optional->biasedNumberBetween(1000000,$amount_goal);
    }
    
    $start_campaign = $faker->optional->dateTimeBetween('now','+3 years');
    $finish_campaign = null;
    if($start_campaign) {
        $finish_campaign = Carbon::instance($start_campaign)->addMonth(1);
    }
    
    $number_of_active_admins = Admin::active()->count();
    
    return [
        'type_id'=>$campaign_type,
        'title'=>$faker->sentence,
        'image'=>$faker->imageUrl(640,480),
        'description'=>$faker->text,
        'amount_goal'=>$amount_goal,
        'amount_real'=>$amount_real,
        'start_campaign'=>$start_campaign,
        'finish_campaign'=>$finish_campaign,
        'fundraising'=>$fundraising,
        'publish'=>$faker->boolean,
        'admin_id'=>$faker->numberBetween(2,$number_of_active_admins),
        'image_file_name'=>$faker->imageUrl(640,480)
    ];
});
