<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableWithRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('fullname')->nullable();
            $table->date('dob')->nullable();
            $table->string('phone')->nullable();
            $table->string('id_type')->nullable();
            $table->string('id_no')->nullable();
            $table->string('employment_status')->nullable();
            $table->text('address')->nullable();
            $table->text('documents')->nullable();
            $table->string('department')->nullable();
            $table->string('role')->nullable(); // Added role field
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'fullname',
                'dob',
                'phone',
                'id_type',
                'id_no',
                'employment_status',
                'address',
                'documents',
                'department',
                'role', // Dropping the role field
            ]);
        });
    }
}
