<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('project_name', 255); // Project name
            $table->string('project_location', 255); // Project location
            $table->string('project_code', 255); // Project code
            $table->timestamp('offer_date')->nullable(); // Nullable timestamp for offer date
            $table->string('end_date'); // End date
            $table->string('status', 255); // Project status
            $table->timestamps(); // Laravel's created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects'); // Revert the migration by dropping the table
    }
}
