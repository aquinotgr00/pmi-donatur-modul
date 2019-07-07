<?php
namespace BajakLautMalaka\PmiDonatur\Seeds;

use Illuminate\Database\Seeder;
use BajakLautMalaka\PmiDonatur\Campaign;

class CampaignsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Campaign::class, 75)->create();
    }
}
