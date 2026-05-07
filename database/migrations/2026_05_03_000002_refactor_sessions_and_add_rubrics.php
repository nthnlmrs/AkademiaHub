<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Move CourseSession from ClassRoom to Course
        Schema::table('course_sessions', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        // Migrate existing data if any (optional but good practice)
        // For simplicity in this dev environment, we'll just drop the old column later
        
        Schema::table('course_sessions', function (Blueprint $table) {
            $table->dropForeign(['class_room_id']);
            $table->dropColumn('class_room_id');
        });

        // 2. Create Grade Rubrics table
        Schema::create('grade_rubrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Assignment, Mid Exam, etc.
            $table->integer('weight'); // Percentage
            $table->timestamps();
        });

        // 3. Update Grades table to include rubric reference
        Schema::table('grades', function (Blueprint $table) {
            $table->foreignId('grade_rubric_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->string('type')->nullable()->after('grade_rubric_id'); // Theory, Lab, etc.
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['grade_rubric_id']);
            $table->dropColumn(['grade_rubric_id', 'type']);
        });

        Schema::dropIfExists('grade_rubrics');

        Schema::table('course_sessions', function (Blueprint $table) {
            $table->foreignId('class_room_id')->nullable()->constrained('class_rooms')->cascadeOnDelete();
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
        });
    }
};
