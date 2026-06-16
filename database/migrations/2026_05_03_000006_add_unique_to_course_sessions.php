<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_sessions', function (Blueprint $table) {
            $table->unique(['course_id', 'session_number']);
        });
    }

    public function down(): void
    {
        Schema::table('course_sessions', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropUnique(['course_id', 'session_number']);
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
        });
    }
};
