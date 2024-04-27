<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyDateAppliedInJobApplications extends Migration
{
    public function up()
    {
        Schema::table('job_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('job_applications', 'date_applied')) {
                $table->timestamp('date_applied')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('job_applications', function (Blueprint $table) {
            if (Schema::hasColumn('job_applications', 'date_applied')) {
                $table->dropColumn('date_applied');
            }
        });
    }
}
