<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('machine_report_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_report_id')->constrained('machine_reports')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_type', 20); // image, video
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('machine_report_media');
    }
}; 