<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedInteger('campaign_id');
            $table->unsignedInteger('donator_id')->nullable();
            $table->unsignedInteger('admin_id')->nullable();
            $table->string('image');
            $table->integer('category');
            $table->bigInteger('amount')->default(0);
            $table->string('pick_method')->nullable();
            $table->string('payment_method')->nullable();
            $table->integer('status')->default(1);
            $table->boolean('guest')->default(false);
            $table->boolean('anonym')->default(false);
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
        Schema::dropIfExists('donations');
    }
}
