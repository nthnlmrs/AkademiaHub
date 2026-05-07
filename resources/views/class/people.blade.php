@section('title', 'People - ' . $classroom->course->name)

<x-app-layout>
    <div class="space-y-10">
        <x-class-header :classroom="$classroom" activeTab="people" />

        <!-- Teachers Section -->
        <section class="space-y-4">
            <h3 class="text-xl font-bold text-slate-800 border-b border-slate-200 pb-2">Teachers</h3>
            
            <div class="space-y-1">
                <!-- Lecturers -->
                @foreach($classroom->lecturers as $lecturer)
                    <div class="flex items-center justify-between py-3 border-b border-slate-50 px-2">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-sm">
                                {{ substr($lecturer->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $lecturer->name }}</p>
                                <p class="text-xs text-slate-500">{{ $lecturer->nim_nip }}</p>
                            </div>
                        </div>
                        <span class="text-[0.65rem] font-bold text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2 py-1 rounded">Primary Instructor</span>
                    </div>
                @endforeach

                <!-- TAs -->
                @foreach($classroom->teachingAssistants as $ta)
                    <div class="flex items-center justify-between py-3 border-b border-slate-50 px-2">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-sm">
                                {{ substr($ta->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $ta->name }}</p>
                                <p class="text-xs text-slate-500">{{ $ta->nim_nip }} &middot; {{ $ta->ta_id ?? 'TA' }}</p>
                            </div>
                        </div>
                        <span class="text-[0.65rem] font-bold text-emerald-600 uppercase tracking-widest bg-emerald-50 px-2 py-1 rounded">Teaching Assistant</span>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Students Section -->
        <section class="space-y-4">
            <h3 class="text-xl font-bold text-slate-800 border-b border-slate-200 pb-2">Students</h3>
            
            <div class="space-y-1">
                @forelse($classroom->students as $student)
                    <div class="flex items-center justify-between py-3 border-b border-slate-50 px-2">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold text-sm">
                                {{ substr($student->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $student->name }}</p>
                                <p class="text-xs text-slate-500">{{ $student->nim_nip }}</p>
                            </div>
                        </div>
                        <div class="text-slate-300">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                    </div>
                @empty
                    <p class="text-center py-10 text-slate-400 italic text-sm">No students enrolled yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
