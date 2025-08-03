<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_breaks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('daily_schedule_id');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            $table->foreign('daily_schedule_id', 'work_breaks_schedule_id_fk')
                ->references('id')
                ->on('daily_schedules')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_breaks');
    }
};
