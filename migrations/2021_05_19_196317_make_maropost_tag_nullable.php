<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeMaropostTagNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(config('lead-tracker.database_connection_name'))
            ->table(
                'leadtracker_leads',
                function (Blueprint $table) {
                    $table->charset = config('lead-tracker.charset');
                    $table->collation = config('lead-tracker.collation');

                    $table->string('maropost_tag_name')->nullable()->change();
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
        Schema::connection(config('lead-tracker.database_connection_name'))
            ->table(
                'leadtracker_leads',
                function (Blueprint $table) {
                    $table->charset = config('lead-tracker.charset');
                    $table->collation = config('lead-tracker.collation');

                    $table->string('maropost_tag_name')->nullable(false)->change();
                }
            );
    }
}