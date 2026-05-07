<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create hardcoded admin account
        User::updateOrCreate(
            ['email' => 'admin@public.com'],
            [
                'name' => 'Administrator',
                'email' => 'admin@public.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'nim_nip' => 'ADMIN001',
                'email_verified_at' => now(),
            ]
        );

        // Create hardcoded admin laboratory account
        User::updateOrCreate(
            ['email' => 'adminlab@public.com'],
            [
                'name' => 'Admin Laboratory',
                'email' => 'adminlab@public.com',
                'password' => Hash::make('adminlab123'),
                'role' => 'admin_lab',
                'nim_nip' => 'ADMINLAB001',
                'email_verified_at' => now(),
            ]
        );

        // Create a sample student
        User::updateOrCreate(
            ['email' => 'student@public.com'],
            [
                'name' => 'Sample Student',
                'email' => 'student@public.com',
                'password' => Hash::make('student123'),
                'role' => 'student',
                'nim_nip' => '2201123456',
                'email_verified_at' => now(),
            ]
        );

        // Create a sample course
        $course = \App\Models\Course::updateOrCreate(
            ['code' => 'COMP6047'],
            [
                'name' => 'Algorithm and Programming',
                'credits' => 4,
                'description' => 'Fundamental of programming using C language.',
            ]
        );

        // Create a sample classroom
        $classroom = \App\Models\ClassRoom::updateOrCreate(
            ['name' => 'LB20'],
            [
                'course_id' => $course->id,
                'type' => 'LEC',
                'room' => 'L1A',
            ]
        );

        // Enroll student to classroom
        $student = User::where('email', 'student@public.com')->first();
        $classroom->users()->syncWithoutDetaching([$student->id]);
    }
}
