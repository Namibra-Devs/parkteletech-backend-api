<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoldersTable extends Migration
{
    public function up()
    {
        Schema::create('folders', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('folder_name'); // Name of the folder
            $table->string('vendor_name'); // Name of the vendor
            $table->timestamp('offer_date'); // Offer date (timestamp)
            $table->string('offer_link'); // Link to the offer
            $table->string('status'); // Status of the folder
            $table->string('file_path'); // Path to the uploaded file
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('folders'); // Revert the migration by dropping the table
    }
}
