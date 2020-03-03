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

                $table->increments('id');

                $table->string('email')->index();

                $table->string('maropost_tag_name')->index();

                $table->string('form_name')->index();
                $table->text('form_page_url')->index();

                $table->string('utm_source')->index();
                $table->string('utm_medium')->index();
                $table->string('utm_campaign')->index();
                $table->string('utm_term')->index();

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
