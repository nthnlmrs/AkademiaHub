<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_activities', function (Blueprint $col) {
            $col->id();
            $col->foreignId('course_session_id')->constrained()->onDelete('cascade');
            $col->string('type'); // attendance, video, file, link
            $col->string('title');
            $col->string('duration')->nullable();
            $col->string('url')->nullable();
            $col->integer('order')->default(0);
            $col->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_activities');
    }
};
