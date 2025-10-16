<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('machine_report_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_report_id')->constrained('machine_reports')->onDelete('cascade');
            $table->foreignId('action_id')->constrained('actions', 'action_id')->onDelete('cascade');
            $table->timestamps();

            // Add unique constraint to prevent duplicate assignments
            $table->unique(['machine_report_id', 'action_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('machine_report_actions');
    }
}; 