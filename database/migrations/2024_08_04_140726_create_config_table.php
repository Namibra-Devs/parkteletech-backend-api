<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateConfigTable extends Migration
{
    public function up()
    {
        Schema::create('config', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('value');
            $table->timestamps();
        });

        // Insert default quota limit
        DB::table('config')->insert([
            'name' => 'quota_limit',
            'value' => '1048576000000', // Example: 1000 GB in bytes
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('config');
    }
}
