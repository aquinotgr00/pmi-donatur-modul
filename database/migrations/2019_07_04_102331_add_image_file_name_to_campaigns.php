<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImageFileNameToCampaigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            //
        });
        if (Schema::hasColumn('campaigns', 'image_file_name')) {

        }else{
             Schema::table('campaigns', function (Blueprint $table) {                
                $table->string('image_file_name')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('campaigns', 'image_file_name')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->dropColumn('image_file_name');
            });
        }
    }
}
