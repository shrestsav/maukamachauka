<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->text('url')->nullable();
            $table->string('logo')->nullable();
            $table->string('email')->nullable();
            $table->string('cp_name')->nullable()->comment('Contact Person Name');
            $table->string('cp_designation')->nullable();
            $table->string('cp_contact')->nullable();
            $table->smallInteger('status')->default(1)->comment('0: Inactive, 1: Active');
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
        Schema::dropIfExists('brands');
    }
}
