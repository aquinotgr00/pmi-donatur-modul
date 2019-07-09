<?php
namespace BajakLautMalaka\PmiDonatur\Seeds;

use Illuminate\Database\Seeder;
use BajakLautMalaka\PmiDonatur\Donation;

class DonationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Donation::class, 75)->create();
    }
}
