<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

                $table->increments('id');

                $table->string('brand', 191)->nullable()->index();

                $table->string('email', 191)->index();

                $table->string('maropost_tag_name', 191)->index();

                $table->string('form_name', 191)->index();
                $table->text('form_page_url');

                $table->string('utm_source', 191)->nullable()->index();
                $table->string('utm_medium', 191)->nullable()->index();
                $table->string('utm_campaign', 191)->nullable()->index();
                $table->string('utm_term', 191)->nullable()->index();

                $table->timestamp('submitted_at')->index();
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
