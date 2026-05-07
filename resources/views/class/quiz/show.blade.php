<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $quiz->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white shadow-sm ring-1 ring-gray-200">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $quiz->title }}</h3>
                            <p class="text-sm text-gray-500 mt-1">Session: {{ $quiz->session->title }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 uppercase font-semibold tracking-wide">
                                {{ $quiz->questions->count() }} Questions &bull; {{ $quiz->total_points }} Points
                            </span>
                        </div>
                    </div>
                    @if($quiz->description)
                        <p class="text-gray-700 mb-6">{{ $quiz->description }}</p>
                    @endif
                </div>
            </div>

            @if(auth()->user()->isAdmin() || auth()->user()->isLecturer() || auth()->user()->isTeachingAssistant())
                <!-- Lecturer View -->
                <div class="bg-white shadow-sm ring-1 ring-gray-200 p-6 text-center">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Lecturer Dashboard</h3>
                    <p class="text-sm text-gray-500 mb-4">View student submissions and grade essays.</p>
                    <a href="{{ route('quiz.submissions', ['classroom' => $classroom->id, 'quiz' => $quiz->id]) }}" class="inline-block bg-indigo-600 text-white px-4 py-2 text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        View Submissions
                    </a>
                </div>
            @else
                <!-- Student View -->
                @if($attempt)
                    <div class="bg-white shadow-sm ring-1 ring-gray-200 p-6">
                        <div class="text-center py-6 border-b border-gray-200 mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Quiz Completed</h3>
                            <p class="text-gray-600 mb-4">You have successfully submitted this quiz.</p>

                            @if($attempt->status === 'graded')
                                <div class="inline-block bg-green-50 border border-green-200 p-4 min-w-[200px]">
                                    <p class="text-sm text-green-800 font-semibold uppercase tracking-wide mb-1">Your Score</p>
                                    <p class="text-3xl font-bold text-green-600">{{ $attempt->total_score }} <span class="text-lg text-green-800/60">/ {{ $quiz->total_points }}</span></p>
                                </div>
                            @else
                                <div class="inline-block bg-yellow-50 border border-yellow-200 p-4">
                                    <p class="text-sm text-yellow-800 font-semibold uppercase tracking-wide mb-1">Status</p>
                                    <p class="text-lg font-bold text-yellow-600">Pending Review</p>
                                    <p class="text-xs text-yellow-700 mt-2">Your essay questions are awaiting grading by the lecturer.</p>
                                </div>
                            @endif
                        </div>

                        <div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Your Answers</h4>
                            <div class="space-y-6">
                                @foreach($quiz->questions as $index => $question)
                                    @php
                                        $answer = $attempt->answers->where('quiz_question_id', $question->id)->first();
                                    @endphp
                                    <div class="border border-gray-200 p-4 {{ $answer && $answer->points_awarded == $question->points && $question->type == 'mcq' ? 'border-l-4 border-l-green-500' : ($answer && $question->type == 'mcq' && $attempt->status == 'graded' ? 'border-l-4 border-l-red-500' : '') }}">
                                        <div class="flex justify-between items-start mb-2">
                                            <p class="font-medium text-gray-900">{{ $index + 1 }}. {{ $question->question }}</p>
                                            <span class="text-sm font-semibold text-gray-500">{{ $question->points }} pts</span>
                                        </div>

                                        @if($question->type === 'mcq')
                                            <div class="mt-2 space-y-2">
                                                @foreach($question->options as $optIndex => $option)
                                                    <div class="flex items-center gap-2 p-2 {{ $answer && $answer->answer == $optIndex ? 'bg-indigo-50 border border-indigo-200' : 'bg-gray-50' }}">
                                                        <div class="w-4 h-4 border border-gray-300 {{ $answer && $answer->answer == $optIndex ? 'bg-indigo-600' : 'bg-white' }} flex items-center justify-center">
                                                            @if($answer && $answer->answer == $optIndex)
                                                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                            @endif
                                                        </div>
                                                        <span class="text-sm {{ $answer && $answer->answer == $optIndex ? 'text-indigo-900 font-medium' : 'text-gray-700' }}">{{ $option }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if($attempt->status === 'graded')
                                                <div class="mt-3 text-sm {{ $answer && $answer->points_awarded == $question->points ? 'text-green-600' : 'text-red-600' }}">
                                                    Points awarded: {{ $answer ? $answer->points_awarded : 0 }} / {{ $question->points }}
                                                </div>
                                            @endif
                                        @else
                                            <div class="mt-2">
                                                <div class="p-3 bg-gray-50 border border-gray-200 text-sm text-gray-700 whitespace-pre-wrap">{{ $answer ? $answer->answer : 'No answer provided.' }}</div>
                                            </div>
                                            <div class="mt-3 text-sm text-gray-600">
                                                @if($answer && $answer->points_awarded !== null)
                                                    <span class="font-medium text-green-600">Points awarded: {{ $answer->points_awarded }} / {{ $question->points }}</span>
                                                @else
                                                    <span class="italic text-yellow-600">Awaiting grade (Max: {{ $question->points }} pts)</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <form action="{{ route('quiz.submit', ['classroom' => $classroom->id, 'quiz' => $quiz->id]) }}" method="POST" class="bg-white shadow-sm ring-1 ring-gray-200">
                        @csrf
                        <div class="p-6">
                            <div class="space-y-8">
                                @foreach($quiz->questions as $index => $question)
                                    <div class="border border-gray-200 p-5">
                                        <div class="flex justify-between items-start mb-4 border-b border-gray-100 pb-2">
                                            <h4 class="font-bold text-gray-900">Question {{ $index + 1 }}</h4>
                                            <span class="text-sm font-semibold bg-gray-100 px-2 py-1 text-gray-700">{{ $question->points }} Points</span>
                                        </div>

                                        <p class="text-gray-800 font-medium mb-4">{{ $question->question }}</p>

                                        @if($question->type === 'mcq')
                                            <div class="space-y-3">
                                                @foreach($question->options as $optIndex => $option)
                                                    <label class="flex items-start gap-3 p-3 border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors">
                                                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $optIndex }}" required class="mt-1 focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                                        <span class="text-sm text-gray-700">{{ $option }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <div>
                                                <textarea name="answers[{{ $question->id }}]" rows="5" required class="block w-full border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Type your answer here..."></textarea>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end">
                            <button type="submit" onclick="return confirm('Are you sure you want to submit? You cannot change your answers later.')" class="bg-indigo-600 text-white px-6 py-3 text-sm font-bold tracking-wide hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                SUBMIT QUIZ
                            </button>
                        </div>
                    </form>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>