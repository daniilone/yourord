<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blacklist', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_id');
            $table->string('client_email');
            $table->text('reason')->nullable();
            $table->timestamps();
            $table->foreign('master_id')->references('id')->on('masters')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blacklist');
    }
};