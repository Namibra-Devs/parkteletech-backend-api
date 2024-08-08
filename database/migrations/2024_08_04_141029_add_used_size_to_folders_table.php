<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_used_size_to_folders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsedSizeToFoldersTable extends Migration
{
    public function up()
    {
        Schema::table('folders', function (Blueprint $table) {
            $table->bigInteger('used_size')->default(0)->after('parent_id'); // Size in bytes
        });
    }

    public function down()
    {
        Schema::table('folders', function (Blueprint $table) {
            $table->dropColumn('used_size');
        });
    }
}
