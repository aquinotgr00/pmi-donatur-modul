<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        $types = [
            [
                'name' => 'Umum',
                'description' => ''
            ],
            [
                'name' => 'Khusus',
                'description' => ''
            ],
            [
                'name' => 'Bulan Dana',
                'description' => ''
            ]
        ];

        foreach ($types as $key => $type) {
            \BajakLautMalaka\PmiDonatur\CampaignType::create($type);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaign_types');
    }
}
