<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_activity', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique();
            $table->string('image_url', 100);
            $table->string('image_title', 255);
            $table->tinyInteger('status');
            $table->dateTime('create_on');
            $table->dateTime('update_on');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_activity');
    }
};
