<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsCodesTable extends Migration
{
    public function up()
    {
        Schema::create('sms_codes', function (Blueprint $table) {
            $table->id();
            $table->string('user_type'); // 'client' or 'specialist'
            $table->unsignedBigInteger('user_id');
            $table->string('phone');
            $table->string('code', 6);
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sms_codes');
    }
}
