<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProjectsTable extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['master_id']);
            $table->dropColumn('master_id');
            $table->decimal('balance', 10, 2)->default(0.00);
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('master_id')->constrained('specialists')->onDelete('cascade');
            $table->dropColumn('balance');
        });
    }
}
