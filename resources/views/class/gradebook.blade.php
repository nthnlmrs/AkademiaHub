@section('title', 'Gradebook - ' . $classroom->course->name)

<x-app-layout>
    <div class="space-y-8">
        <x-class-header :classroom="$classroom" activeTab="gradebook" />

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Rubric Management / Grade Components (Always visible) -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-4 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest">
                    {{ (Auth::user()->isAdmin() || Auth::user()->isLecturer()) ? 'Manage Rubrics' : 'Grade Components' }}
                </h3>
                
                @if(Auth::user()->isAdmin() || Auth::user()->isLecturer())
                    <form method="POST" action="{{ route('grade.rubric.store', $classroom) }}" class="space-y-3">
                        @csrf
                        <input type="text" name="name" required placeholder="e.g. Assignment, Mid Exam" 
                               class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-orange-500 focus:border-orange-500">
                        <div class="flex gap-2">
                            <input type="number" name="weight" required placeholder="Weight %" 
                                   class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-orange-500 focus:border-orange-500">
                            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-4 rounded-xl font-bold transition-colors">Add</button>
                        </div>
                    </form>
                @endif

                <div class="pt-2 space-y-2">
                    @forelse($rubrics as $rubric)
                        <div class="flex justify-between items-center text-sm p-3 bg-slate-50 rounded-xl">
                            <span class="font-semibold text-slate-700">{{ $rubric->name }}</span>
                            <span class="text-orange-600 font-bold">{{ $rubric->weight }}%</span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-slate-400 text-xs italic">No rubrics created yet.</div>
                    @endforelse
                </div>
            </div>

            <!-- Add Grade Form (Admin/Lecturer/TA Only) OR Student Grade Summary Card (Student Only) -->
            @if(Auth::user()->isAdmin() || Auth::user()->isLecturer() || Auth::user()->isTeachingAssistant())
                <!-- Add Grade Form -->
                <div class="{{ (Auth::user()->isAdmin() || Auth::user()->isLecturer()) ? 'lg:col-span-2' : 'lg:col-span-3' }} bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest mb-6">Add/Update Student Grade</h3>
                    <form method="POST" action="{{ route('grade.store', $classroom) }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @csrf
                        <div class="space-y-1">
                            <label class="text-[0.65rem] font-bold text-slate-400 uppercase">Student</label>
                            <select name="user_id" required class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-orange-500 focus:border-orange-500">
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->nim_nip }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[0.65rem] font-bold text-slate-400 uppercase">Category (Type)</label>
                            <select name="type" required class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-orange-500 focus:border-orange-500">
                                <option value="Theory">Theory</option>
                                <option value="Lab">Lab</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[0.65rem] font-bold text-slate-400 uppercase">Rubric / Weight</label>
                            <select name="grade_rubric_id" required class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-orange-500 focus:border-orange-500">
                                @foreach($rubrics as $rubric)
                                    <option value="{{ $rubric->id }}">{{ $rubric->name }} ({{ $rubric->weight }}%)</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[0.65rem] font-bold text-slate-400 uppercase">Component Name</label>
                            <input type="text" name="component" required placeholder="e.g. Quiz 1, Assignment 1" 
                                   class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-orange-500 focus:border-orange-500">
                        </div>

                        <div class="space-y-1">
                            <label class="text-[0.65rem] font-bold text-slate-400 uppercase">Score</label>
                            <input type="number" step="0.01" name="score" required placeholder="0.00" 
                                   class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-orange-500 focus:border-orange-500">
                        </div>

                        <div class="space-y-1">
                            <label class="text-[0.65rem] font-bold text-slate-400 uppercase">Max Score</label>
                            <input type="number" step="0.01" name="max_score" required value="100" 
                                   class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-orange-500 focus:border-orange-500">
                        </div>

                        <div class="lg:col-span-3 pt-2">
                            <button type="submit" class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-lg shadow-orange-100 transition-all uppercase tracking-widest text-sm">
                                Save Grade
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <!-- Student Summary Card -->
                <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between min-h-[220px]">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest mb-3">Grade Summary</h3>
                        <p class="text-xs text-slate-500 leading-relaxed mb-6">
                            This panel displays your overall academic standing in this course. Grades are calculated based on the weight of each component.
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-auto">
                        <div class="p-4 bg-slate-50 rounded-2xl flex flex-col justify-center">
                            <span class="block text-[0.65rem] font-bold text-slate-400 uppercase">Total Weight Checked</span>
                            <span class="text-2xl font-bold text-slate-700">
                                {{ $rubrics->sum('weight') }}%
                            </span>
                        </div>
                        <div class="p-4 bg-orange-500 text-white rounded-2xl flex flex-col justify-center">
                            <span class="block text-[0.65rem] font-bold text-white/70 uppercase">Estimated Final Grade</span>
                            <span class="text-2xl font-black">
                                @php
                                    $finalGrade = 0;
                                    $weightSum = 0;
                                    foreach($rubrics as $rubric) {
                                        $grade = $grades->where('grade_rubric_id', $rubric->id)->first();
                                        if ($grade) {
                                            $finalGrade += ($grade->score / $grade->max_score) * $rubric->weight;
                                            $weightSum += $rubric->weight;
                                        }
                                    }
                                @endphp
                                {{ $weightSum > 0 ? round(($finalGrade / $weightSum) * 100, 1) . '%' : '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Grades Table -->
        <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
            @if($grades->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Student</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Category</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Rubric</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Component</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-center">Score</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($grades as $grade)
                                <tr class="hover:bg-slate-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-700">{{ $grade->user->name }}</div>
                                        <div class="text-[0.65rem] text-slate-400 font-mono">{{ $grade->user->nim_nip }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-slate-100 text-slate-600 text-[0.65rem] font-bold uppercase rounded">
                                            {{ $grade->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-slate-600">{{ $grade->gradeRubric->name }}</div>
                                        <div class="text-[0.65rem] text-orange-500 font-bold">{{ $grade->gradeRubric->weight }}% Weight</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        {{ $grade->component }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex items-center gap-2">
                                            <span class="text-lg font-bold text-slate-800">{{ round($grade->score, 1) }}</span>
                                            <span class="text-xs text-slate-500">/ {{ round($grade->max_score, 0) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if(Auth::user()->isAdmin() || Auth::user()->isLecturer())
                                            <form method="POST" action="{{ route('grade.destroy', $grade) }}" onsubmit="return confirm('Remove this grade?')">
                                                @csrf @method('DELETE')
                                                <button class="text-slate-300 hover:text-red-500 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-20 text-center">
                    <p class="text-slate-400 italic">No grades recorded yet.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
