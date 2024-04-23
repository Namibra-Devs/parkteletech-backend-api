<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_details', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('email');
            // $table->string('email')->unique();
            $table->date('dob');
            $table->string('phone');
            $table->string('id_type');
            $table->string('id_no');
            // $table->string('id_no')->unique();
            $table->string('employment_status');
            $table->text('address');
            $table->text('documents')->nullable();
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
        Schema::dropIfExists('staff_details');
    }
}
