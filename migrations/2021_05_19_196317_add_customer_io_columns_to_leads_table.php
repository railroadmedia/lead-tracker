<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerIoColumnsToLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'leadtracker_leads',
            function (Blueprint $table) {
                $table->charset = config('lead-tracker.charset');
                $table->collation = config('lead-tracker.collation');

                $table->string('maropost_tag_name')->nullable()->change();

                $table->string('customer_io_customer_id', 191)->nullable()->after('maropost_tag_name')->index();
                $table->string('customer_io_event_name', 191)->nullable()->after('customer_io_customer_id')->index();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'leadtracker_leads',
            function (Blueprint $table) {
                $table->dropColumn('customer_io_customer_id');
                $table->dropColumn('customer_io_event_name');
            }
        );
    }
}