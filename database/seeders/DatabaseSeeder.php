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

        // Create Creativity and Innovation course
        $creativityCourse = \App\Models\Course::updateOrCreate(
            ['code' => 'ENTR6003'],
            [
                'name' => 'Creativity and Innovation',
                'credits' => 4,
                'description' => 'Unlocking creative potential and learning structured innovation processes.',
            ]
        );

        // Create a classroom for it
        $creativityClassroom = \App\Models\ClassRoom::updateOrCreate(
            ['name' => 'LA01'],
            [
                'course_id' => $creativityCourse->id,
                'type' => 'LEC',
                'room' => 'L2B',
            ]
        );

        // Enroll student to this classroom
        $creativityClassroom->users()->syncWithoutDetaching([$student->id]);

        // Create sessions for Creativity and Innovation
        $sessionsData = [
            [
                'session_number' => 1,
                'title' => 'Introduction to Creativity and Innovation Course',
                'description' => "This session covers the basic syllabus and requirements.\nStudents will learn about the differences between creativity and innovation.",
                'interactive_text' => 'Welcome to the Creativity and Innovation course. In this introductory session, we will explore the fundamental concepts of creative thinking and how it translates into innovation. You will understand the course objectives, syllabus, expectations, and how this journey will transform your approach to problem-solving. Let us get ready to unlock your creative potential!',
            ],
            [
                'session_number' => 2,
                'title' => 'Design Thinking for SDGs',
                'description' => "Introduction to Design Thinking framework.\nApplying Design Thinking to solve Sustainable Development Goals.",
                'interactive_text' => 'Design thinking is a human-centered approach to innovation that integrates the needs of people, the possibilities of technology, and the requirements for business success. Today, we will focus on applying design thinking methodologies to address Sustainable Development Goals. We will analyze real-world challenges, empathize with affected communities, and define critical problems.',
            ],
            [
                'session_number' => 3,
                'title' => 'Customer Insights',
                'description' => "How to conduct user interviews.\nAnalyzing customer behavior and creating empathy maps.",
                'interactive_text' => 'Understanding your customer is the key to creating meaningful innovations. In this session, we will dive deep into customer insights. We will learn how to conduct empathy interviews, observe customer behaviors, and translate those observations into actionable insights using empathy maps and user persona templates.',
            ],
            [
                'session_number' => 4,
                'title' => 'Idea Generation Techniques',
                'description' => "Brainstorming and SCAMPER techniques.\nSelecting and filtering ideas.",
                'interactive_text' => 'Generate as many ideas as possible without judgment! Today we will practice various ideation techniques including brainstorming, brainwriting, mind mapping, and the SCAMPER method. You will collaborate in teams to stretch your imagination and then learn how to screen and filter these ideas using systematic evaluation matrices.',
            ],
            [
                'session_number' => 5,
                'title' => 'Value Creation',
                'description' => "Defining value proposition.\nIntroduction to Value Proposition Canvas.",
                'interactive_text' => 'How does your idea create value for the customer? We will explore the concept of value creation and study the Value Proposition Canvas in detail. You will learn how to align your product features and services with the jobs, pains, and gains of your target customer segment.',
            ],
            [
                'session_number' => 6,
                'title' => 'Business Idea Discussion',
                'description' => "Presenting and critiquing team business ideas.\nPeer feedback session.",
                'interactive_text' => 'In this interactive session, teams will present their preliminary business ideas. We will engage in constructive peer feedback and group discussions. This is an opportunity to refine your concepts, address potential flaws, and explore new directions based on diverse viewpoints and collaborative inputs.',
            ],
            [
                'session_number' => 7,
                'title' => 'Market Positioning',
                'description' => "Competitor analysis.\nDefining unique selling proposition (USP) and brand positioning.",
                'interactive_text' => 'Where does your product stand in the competitive landscape? We will cover market positioning strategies, competitor analysis, and how to identify your unique selling proposition. You will learn to construct positioning maps and draft compelling positioning statements that distinguish your brand from others.',
            ],
            [
                'session_number' => 8,
                'title' => 'Revenue Streams and Cost Structure',
                'description' => "Understanding business model financials.\nIdentifying key revenue sources and cost drivers.",
                'interactive_text' => 'A great idea needs a viable business model to survive. This session focuses on the financial components of the Business Model Canvas. We will examine different revenue streams, pricing mechanisms, and analyze the key cost drivers associated with operating and scaling your business venture.',
            ],
            [
                'session_number' => 9,
                'title' => 'Prototype Creation',
                'description' => "Building low-fidelity prototypes.\nUsing tools like Figma, paper, or wireframes.",
                'interactive_text' => 'A prototype is worth a thousand meetings. Today, we turn ideas into tangible artifacts. We will discuss the principles of prototyping, including low-fidelity and rapid prototyping techniques. Teams will start building their initial prototypes using paper, digital wireframes, or mockup tools.',
            ],
            [
                'session_number' => 10,
                'title' => 'Prototype Testing',
                'description' => "Testing prototype with users.\nCollecting feedback and planning iterations.",
                'interactive_text' => 'Testing is an opportunity to learn about your solution and your user. In our final session, we will learn how to design and execute user tests. You will gather feedback, observe user interactions with your prototype, and document key findings to plan the next design iterations.',
            ],
        ];

        foreach ($sessionsData as $sData) {
            $creativityCourse->courseSessions()->updateOrCreate(
                ['session_number' => $sData['session_number']],
                $sData
            );
        }

        // Create Grade Rubrics for Creativity and Innovation
        $rubricsData = [
            ['name' => 'Assignment', 'weight' => 20],
            ['name' => 'UTS', 'weight' => 30],
            ['name' => 'UAS', 'weight' => 30],
            ['name' => 'Final Project', 'weight' => 20],
        ];

        $rubrics = [];
        foreach ($rubricsData as $rData) {
            $rubrics[$rData['name']] = \App\Models\GradeRubric::updateOrCreate(
                [
                    'course_id' => $creativityCourse->id,
                    'name' => $rData['name'],
                ],
                ['weight' => $rData['weight']]
            );
        }
    }
}
