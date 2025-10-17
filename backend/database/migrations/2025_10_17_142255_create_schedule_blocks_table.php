<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_blocks', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('start_time')->nullable(); // null = dia inteiro bloqueado
            $table->time('end_time')->nullable();
            $table->string('reason')->nullable();
            $table->enum('type', ['full_day', 'time_range'])->default('full_day');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['date', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_blocks');
    }
}
