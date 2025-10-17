<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateRangeToScheduleBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_blocks', function (Blueprint $table) {
            $table->date('end_date')->nullable()->after('date');
            $table->enum('block_mode', ['single_date', 'date_range'])->default('single_date')->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_blocks', function (Blueprint $table) {
            $table->dropColumn(['end_date', 'block_mode']);
        });
    }
}
