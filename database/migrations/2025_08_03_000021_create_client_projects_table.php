<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('project_id');
            $table->timestamps();
            $table->foreign('client_id', 'client_projects_client_id_fk')
                ->references('id')
                ->on('clients')
                ->onDelete('cascade');
            $table->foreign('project_id', 'client_projects_project_id_fk')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');
            $table->unique(['client_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_projects');
    }
};
