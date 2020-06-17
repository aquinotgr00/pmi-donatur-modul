<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNotesToDonations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('donations', 'notes')) {
             Schema::table('donations', function (Blueprint $table) {                
                $table->string('notes')->nullable();
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
        if (Schema::hasColumn('donations', 'notes')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->dropColumn('notes');
            });
        }
    }
}
