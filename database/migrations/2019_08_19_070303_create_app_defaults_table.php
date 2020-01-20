<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppDefaultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_defaults', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('OTP_expiry')->unsigned()->comment('Minute');
            $table->string('contact');
            $table->string('company_email');
            $table->string('company_logo');
            $table->longText('TACS')->comment('TERMS AND CONDITIONS');
            $table->longText('FAQS')->comment('FREQUENTLY ASKED QUESTIONS');
            $table->integer('app_rows');
            $table->integer('sys_rows');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_defaults');
    }
}
