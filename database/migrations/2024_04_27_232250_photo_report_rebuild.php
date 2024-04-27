<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PhotoReportRebuild extends Migration
{
    public function up()
    {
        Schema::create('photo_reports', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('project_name', 255); // Project name
            $table->timestamp('completion_date'); // Completion date
            $table->text('description'); // Description of the photo report
            $table->json('file_paths'); // JSON-encoded array of file paths for multiple uploads
            $table->string('status');
            $table->timestamps(); // Laravel's created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('photo_reports'); // Revert the migration by dropping the table
    }
}
