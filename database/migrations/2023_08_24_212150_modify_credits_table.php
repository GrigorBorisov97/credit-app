<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->dropColumn('amount');
            $table->decimal('credit_amount', 10, 2)->after('user_id');
            $table->decimal('refund_amount', 10, 2)->after('credit_amount');
        });
    }

    public function down()
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->dropColumn('credit_amount');
            $table->dropColumn('refund_amount');
            $table->decimal('amount', 10, 2)->after('user_id');
        });
    }
};
