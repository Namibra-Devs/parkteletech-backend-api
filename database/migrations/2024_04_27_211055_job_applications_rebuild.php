<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JobApplicationsRebuild extends Migration
{
    public function up()
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('job_id'); // Job reference
            $table->string('applicant_name', 255); // Applicant's name
            $table->string('status', 255); // Application status
            $table->timestamp('date_applied')->useCurrent(); // Default to current timestamp
            $table->timestamps(); // Adds created_at and updated_at

            // Foreign key constraint
            $table->foreign('job_id')->references('id')->on('job_postings')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_applications'); // Drop the table to revert the migration
    }
}
