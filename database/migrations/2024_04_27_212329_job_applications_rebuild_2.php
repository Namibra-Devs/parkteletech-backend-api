<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JobApplicationsRebuild2 extends Migration
{
    public function up()
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->timestamp('date_applied')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('your_table_name', function (Blueprint $table) {
            // Reverse the changes made in the 'up' method
            $table->string('data_applied')->useCurrent(); 

            // Reverse other modifications if needed
        });
    }
}
