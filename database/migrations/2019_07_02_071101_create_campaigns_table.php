<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('admin_id');
            $table->unsignedInteger('type_id');
            $table->string('title')->unique();
            $table->string('image');
            $table->text('description');
            $table->bigInteger('amount_goal')->nullable();
            $table->bigInteger('amount_real')->nullable();
            $table->date('start_campaign')->nullable();
            $table->date('finish_campaign')->nullable();
            $table->boolean('fundraising')->default(false);
            $table->boolean('publish')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaigns');
    }
}
