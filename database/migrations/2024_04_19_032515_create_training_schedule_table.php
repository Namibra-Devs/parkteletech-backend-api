<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_schedules', function (Blueprint $table) {
            $table->id();
            $table->string("training_topic");
            $table->string("desc");
            $table->string("time");
            $table->string("date");
            $table->string("department");
            $table->string("individuals");
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
        Schema::dropIfExists('training_schedules');
    }
}
