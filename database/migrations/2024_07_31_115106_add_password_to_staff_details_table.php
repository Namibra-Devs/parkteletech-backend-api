<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPasswordToStaffDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('staff_details', function (Blueprint $table) {
            $table->string('password')->nullable()->after('department');
        });
    }

    public function down()
    {
        Schema::table('staff_details', function (Blueprint $table) {
            $table->dropColumn('password');
        });
    }
}
