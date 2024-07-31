<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFolderInternalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folder_internals', function (Blueprint $table) {
            $table->id(); // Adds an auto-incrementing primary key
            $table->string('folder_name');
            $table->string('vendor_name');
            $table->timestamp('offer_date')->nullable(); // Allows null values for offer_date
            $table->string('offer_link')->nullable(); // Allows null values for offer_link
            $table->string('status')->nullable(); // Allows null values for status
            $table->string('file_path')->nullable(); // Allows null values for file_path
            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folder_internals');
    }
}
