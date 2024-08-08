<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_used_quota_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsedQuotaToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('used_quota')->default(0)->after('email'); // Size in bytes
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('used_quota');
        });
    }
}
