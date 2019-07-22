<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvoiceIdToDonations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('donations', 'invoice_id')) {

        }else{
             Schema::table('donations', function (Blueprint $table) {                
                $table->string('invoice_id')->nullable();
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
        if (Schema::hasColumn('donations', 'invoice_id')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->dropColumn('invoice_id');
            });
        }
    }
}
