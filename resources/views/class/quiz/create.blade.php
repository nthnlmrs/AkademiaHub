<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Quiz for Session') }} {{ $session->session_number }}: {{ $session->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm ring-1 ring-gray-200">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('quiz.store', ['classroom' => $classroom->id, 'session' => $session->id]) }}" method="POST" id="quiz-form">
                        @csrf

                        <!-- Quiz Details -->
                        <div class="mb-6">
                            <label class="block font-medium text-sm text-gray-700" for="title">Quiz Title</label>
                            <input type="text" name="title" id="title" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 block w-full shadow-sm sm:text-sm mt-1" required value="{{ old('title') }}">
                            @error('title')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block font-medium text-sm text-gray-700" for="description">Description (Optional)</label>
                            <textarea name="description" id="description" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 block w-full shadow-sm sm:text-sm mt-1">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Questions Builder -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Questions</h3>
                                <div class="text-sm font-medium text-gray-700">
                                    Total Points: <span id="total-points">0</span> / 100
                                </div>
                            </div>

                            @error('questions')
                                <div class="bg-red-50 text-red-600 p-3 mb-4 text-sm">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div id="questions-container" class="space-y-6">
                                <!-- Questions will be added here -->
                            </div>

                            <div class="mt-4 flex gap-2">
                                <button type="button" id="add-mcq" class="bg-gray-100 text-gray-700 px-4 py-2 text-sm font-medium border border-gray-300 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    + Add Multiple Choice
                                </button>
                                <button type="button" id="add-essay" class="bg-gray-100 text-gray-700 px-4 py-2 text-sm font-medium border border-gray-300 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    + Add Essay
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-end border-t border-gray-200 pt-6">
                            <a href="{{ route('class.session', ['classroom' => $classroom->id, 'session' => $session->id]) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Save Quiz
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates -->
    <template id="mcq-template">
        <div class="question-item border border-gray-200 p-4 bg-gray-50 relative">
            <button type="button" class="remove-question absolute top-4 right-4 text-gray-400 hover:text-red-500">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <input type="hidden" name="questions[__INDEX__][type]" value="mcq">

            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-10">
                    <label class="block text-sm font-medium text-gray-700">Question Text</label>
                    <textarea name="questions[__INDEX__][question]" required class="mt-1 block w-full border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" rows="2"></textarea>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Points</label>
                    <input type="number" name="questions[__INDEX__][points]" required min="1" max="100" class="point-input mt-1 block w-full border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Options & Correct Answer</label>
                <div class="space-y-2 options-container">
                    <div class="flex items-center gap-2">
                        <input type="radio" name="questions[__INDEX__][correct_answer]" value="0" required class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                        <input type="text" name="questions[__INDEX__][options][]" placeholder="Option 1" required class="block w-full border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="radio" name="questions[__INDEX__][correct_answer]" value="1" required class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                        <input type="text" name="questions[__INDEX__][options][]" placeholder="Option 2" required class="block w-full border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>
                <button type="button" class="add-option mt-2 text-sm text-indigo-600 hover:text-indigo-900">+ Add Option</button>
            </div>
        </div>
    </template>

    <template id="essay-template">
        <div class="question-item border border-gray-200 p-4 bg-gray-50 relative">
            <button type="button" class="remove-question absolute top-4 right-4 text-gray-400 hover:text-red-500">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <input type="hidden" name="questions[__INDEX__][type]" value="essay">

            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-10">
                    <label class="block text-sm font-medium text-gray-700">Essay Question / Prompt</label>
                    <textarea name="questions[__INDEX__][question]" required class="mt-1 block w-full border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" rows="3"></textarea>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Max Points</label>
                    <input type="number" name="questions[__INDEX__][points]" required min="1" max="100" class="point-input mt-1 block w-full border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let questionIndex = 0;
            const container = document.getElementById('questions-container');
            const totalPointsEl = document.getElementById('total-points');

            function updateTotalPoints() {
                const inputs = document.querySelectorAll('.point-input');
                let total = 0;
                inputs.forEach(input => {
                    total += parseInt(input.value || 0, 10);
                });
                totalPointsEl.textContent = total;
                if (total > 100) {
                    totalPointsEl.classList.add('text-red-600', 'font-bold');
                } else {
                    totalPointsEl.classList.remove('text-red-600', 'font-bold');
                }
            }

            container.addEventListener('input', function(e) {
                if (e.target.classList.contains('point-input')) {
                    updateTotalPoints();
                }
            });

            document.getElementById('add-mcq').addEventListener('click', function() {
                const template = document.getElementById('mcq-template').innerHTML;
                const html = template.replace(/__INDEX__/g, questionIndex++);
                container.insertAdjacentHTML('beforeend', html);
            });

            document.getElementById('add-essay').addEventListener('click', function() {
                const template = document.getElementById('essay-template').innerHTML;
                const html = template.replace(/__INDEX__/g, questionIndex++);
                container.insertAdjacentHTML('beforeend', html);
            });

            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-question')) {
                    e.target.closest('.question-item').remove();
                    updateTotalPoints();
                }

                if (e.target.classList.contains('add-option')) {
                    const item = e.target.closest('.question-item');
                    const optionsContainer = item.querySelector('.options-container');
                    const match = item.innerHTML.match(/questions\[(\d+)\]/);
                    if (match) {
                        const idx = match[1];
                        const optionCount = optionsContainer.children.length;
                        const optionHtml = `
                            <div class="flex items-center gap-2 mt-2">
                                <input type="radio" name="questions[${idx}][correct_answer]" value="${optionCount}" required class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                <input type="text" name="questions[${idx}][options][]" placeholder="Option ${optionCount + 1}" required class="block w-full border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        `;
                        optionsContainer.insertAdjacentHTML('beforeend', optionHtml);
                    }
                }
            });

            document.getElementById('quiz-form').addEventListener('submit', function(e) {
                const inputs = document.querySelectorAll('.point-input');
                let total = 0;
                inputs.forEach(input => {
                    total += parseInt(input.value || 0, 10);
                });
                if (total > 100) {
                    e.preventDefault();
                    alert('Total points cannot exceed 100. Current total is ' + total + '.');
                }
            });
        });
    </script>
</x-app-layout>