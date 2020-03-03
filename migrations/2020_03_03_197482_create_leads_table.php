<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'leadtracker_leads',
            function (Blueprint $table) {
                $table->charset = config('lead-tracker.charset');
                $table->collation = config('lead-tracker.collation');

                $table->bigIncrements('id');

                $table->string('uuid', 64)->unique()->index();
                $table->string('cookie_id', 64)->index()->nullable();

                $table->unsignedBigInteger('user_id')->unsigned()->index()->nullable();

                $table->string('url_protocol', 32)->index();
                $table->string('url_domain', 128)->index();
                $table->string('url_path', 191)->index();

                $table->string('referer_url_protocol', 32)->index()->nullable();
                $table->string('referer_url_domain', 128)->index()->nullable();
                $table->string('referer_url_path', 191)->index()->nullable();

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
        Schema::dropIfExists('leadtracker_leads');
    }
}
