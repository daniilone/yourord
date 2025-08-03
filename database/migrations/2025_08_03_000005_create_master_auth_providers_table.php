<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_auth_providers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_id');
            $table->string('provider', 50);
            $table->string('provider_id');
            $table->timestamps();
            $table->unique(['provider', 'provider_id']);
            $table->foreign('master_id')->references('id')->on('masters')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_auth_providers');
    }
};
