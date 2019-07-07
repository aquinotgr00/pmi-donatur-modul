<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyCampaignUserToAdminId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('campaigns', 'user_id')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->dropColumn('user_id');
                $table->unsignedInteger('admin_id');
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
        if (Schema::hasColumn('campaigns', 'admin_id')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->dropColumn('admin_id');
            });
        }
    }
}
