<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_schedule_template_breaks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('daily_schedule_template_id');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            $table->foreign('daily_schedule_template_id', 'dst_breaks_template_id_fk')
                ->references('id')
                ->on('daily_schedule_templates')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_schedule_template_breaks');
    }
};
