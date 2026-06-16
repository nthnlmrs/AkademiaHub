<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Course;
use App\Models\ClassRoom;
use App\Models\GradeRubric;
use App\Models\CourseSession;

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
        $student = User::updateOrCreate(
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

        // CREATE A BUNCH OF USERS
        $users = User::factory()->count(10)->create([
            'role' => 'student'
        ]);

        $lecturers = User::factory()->count(5)->create([
            'role' => 'lecturer'
        ]);

        // Create multiple courses
        $coursesData = [
            ['code' => 'COMP6047', 'name' => 'Algorithm and Programming', 'credits' => 4, 'description' => 'Fundamental of programming using C language.'],
            ['code' => 'ENTR6003', 'name' => 'Creativity and Innovation', 'credits' => 4, 'description' => 'Unlocking creative potential and learning structured innovation processes.'],
            ['code' => 'ISYS6181', 'name' => 'Management Information Systems', 'credits' => 4, 'description' => 'Business and enterprise systems management.'],
            ['code' => 'COMP6048', 'name' => 'Data Structures', 'credits' => 4, 'description' => 'Advanced data organization and storage.'],
            ['code' => 'STAT6026', 'name' => 'Probability and Statistics', 'credits' => 2, 'description' => 'Foundations of mathematical statistics.'],
            ['code' => 'CHAR6013', 'name' => 'Character Building', 'credits' => 2, 'description' => 'Developing soft skills and professional ethics.'],
            ['code' => 'LANG6027', 'name' => 'Indonesian', 'credits' => 2, 'description' => 'Formal Indonesian language course.'],
            ['code' => 'LANG6061', 'name' => 'English', 'credits' => 2, 'description' => 'Professional English communication.'],
            ['code' => 'MATH6025', 'name' => 'Discrete Mathematics', 'credits' => 2, 'description' => 'Mathematics for computer science.'],
            ['code' => 'MATH6030', 'name' => 'Linear Algebra', 'credits' => 2, 'description' => 'Vectors, matrices, and linear equations.'],
        ];

        $courses = [];
        foreach ($coursesData as $c) {
            $courses[] = Course::updateOrCreate(['code' => $c['code']], $c);
        }

        // Create multiple classrooms and enroll the student
        $classrooms = [];
        $types = ['LEC', 'LAB'];
        foreach ($courses as $index => $course) {
            $type = $types[$index % 2];
            $classroom = ClassRoom::updateOrCreate(
                ['name' => 'LA' . str_pad($index + 1, 2, '0', STR_PAD_LEFT), 'course_id' => $course->id],
                [
                    'type' => $type,
                    'room' => 'R' . rand(100, 999),
                ]
            );

            // Enroll main student
            $classroom->users()->syncWithoutDetaching([$student->id]);

            // Enroll other random students
            $randomUsers = $users->random(rand(2, 5))->pluck('id')->toArray();
            $classroom->users()->syncWithoutDetaching($randomUsers);

            // Assign lecturers
            $randomLecturer = $lecturers->random()->id;
            $classroom->users()->syncWithoutDetaching([$randomLecturer]);

            $classrooms[] = $classroom;

            // Generate Sessions for each course
            for ($i = 1; $i <= 10; $i++) {
                CourseSession::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'session_number' => $i
                    ],
                    [
                        'title' => "Session $i - {$course->name}",
                        'description' => "Description and learning materials for session $i.",
                        'interactive_text' => "Welcome to session $i of {$course->name}. This text is available for the read-aloud functionality.",
                    ]
                );
            }

            // Create Rubrics for each course
            $rubricsData = [
                ['name' => 'Assignment', 'weight' => 20],
                ['name' => 'Mid Exam', 'weight' => 30],
                ['name' => 'Final Exam', 'weight' => 50],
            ];
            foreach ($rubricsData as $r) {
                GradeRubric::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'name' => $r['name'],
                    ],
                    ['weight' => $r['weight']]
                );
            }
        }
    }
}
