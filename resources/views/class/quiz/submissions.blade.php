<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Submissions: {{ $quiz->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white shadow-sm ring-1 ring-gray-200">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $quiz->title }} Submissions</h3>
                            <p class="text-sm text-gray-500 mt-1">Total Points Possible: {{ $quiz->total_points }}</p>
                        </div>
                        <a href="{{ route('quiz.show', ['classroom' => $classroom->id, 'quiz' => $quiz->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                            &larr; Back to Quiz
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm ring-1 ring-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Student Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Total Score</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Submitted At</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($attempts as $attempt)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $attempt->user->name }}
                                        <div class="text-xs text-gray-500 font-normal">{{ $attempt->user->nim_nip }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($attempt->status === 'graded')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold bg-green-100 text-green-800 uppercase tracking-wider">
                                                Graded
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold bg-yellow-100 text-yellow-800 uppercase tracking-wider">
                                                Pending Review
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                        {{ $attempt->total_score }} / {{ $quiz->total_points }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $attempt->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="document.getElementById('grade-modal-{{ $attempt->id }}').classList.remove('hidden')" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $attempt->status === 'graded' ? 'Review / Edit' : 'Grade Essays' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 italic">
                                        No submissions yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals for grading -->
    @foreach($attempts as $attempt)
        <div id="grade-modal-{{ $attempt->id }}" class="hidden fixed inset-0 z-[100] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900/75" onclick="document.getElementById('grade-modal-{{ $attempt->id }}').classList.add('hidden')"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white shadow-xl sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full sm:p-6 border border-gray-200">
                    <div class="flex justify-between items-center mb-5 border-b border-gray-200 pb-4">
                        <h3 class="text-lg font-bold text-gray-900">Grading: {{ $attempt->user->name }}</h3>
                        <button onclick="document.getElementById('grade-modal-{{ $attempt->id }}').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('quiz.grade', ['classroom' => $classroom->id, 'quiz' => $quiz->id, 'attempt' => $attempt->id]) }}" method="POST">
                        @csrf
                        <div class="space-y-6 max-h-[60vh] overflow-y-auto pr-2">
                            @foreach($attempt->answers as $index => $answer)
                                <div class="bg-gray-50 border border-gray-200 p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <p class="font-medium text-gray-900 text-sm"><span class="text-gray-500 mr-2">Q{{ $loop->iteration }}.</span>{{ $answer->question->question }}</p>
                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wide bg-gray-200 px-2 py-1">{{ $answer->question->type }} | Max: {{ $answer->question->points }} pts</span>
                                    </div>

                                    @if($answer->question->type === 'mcq')
                                        <div class="mt-2 text-sm text-gray-700">
                                            Student Answer:
                                            @if($answer->answer !== null && isset($answer->question->options[$answer->answer]))
                                                <span class="font-medium">{{ $answer->question->options[$answer->answer] }}</span>
                                            @else
                                                <span class="italic">No Answer</span>
                                            @endif
                                        </div>
                                        <div class="mt-2 text-sm font-semibold {{ $answer->points_awarded == $answer->question->points ? 'text-green-600' : 'text-red-600' }}">
                                            Auto-Graded Score: {{ $answer->points_awarded }} / {{ $answer->question->points }}
                                        </div>
                                    @else
                                        <div class="mt-2">
                                            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Student's Essay Response:</p>
                                            <div class="bg-white p-3 border border-gray-200 text-sm text-gray-800 whitespace-pre-wrap min-h-[60px]">{{ $answer->answer }}</div>
                                        </div>
                                        <div class="mt-4 flex items-center justify-end gap-3">
                                            <label class="text-sm font-bold text-gray-700">Award Points:</label>
                                            <input type="number" name="grades[{{ $answer->id }}]" value="{{ $answer->points_awarded }}" min="0" max="{{ $answer->question->points }}" step="0.5" class="w-24 text-right border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                            <span class="text-sm text-gray-500">/ {{ $answer->question->points }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end gap-3">
                            <button type="button" onclick="document.getElementById('grade-modal-{{ $attempt->id }}').classList.add('hidden')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Cancel
                            </button>
                            <button type="submit" class="bg-indigo-600 border border-transparent text-white px-4 py-2 text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Save Grades
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</x-app-layout>