<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesInternalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files_internals', function (Blueprint $table) {
            $table->id(); // Adds an auto-incrementing primary key
            $table->unsignedBigInteger('staff_detail_id'); // Column for staff detail ID
            $table->string('name'); // Column for file name
            $table->string('path'); // Column for file path
            $table->timestamps(); // Adds created_at and updated_at columns

            // Optional: Define a foreign key constraint if `staff_detail_id` references another table
            // $table->foreign('staff_detail_id')->references('id')->on('staff_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files_internals'); // Drops the table if it exists
    }
}
