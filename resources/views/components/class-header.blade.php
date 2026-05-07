@props(['classroom', 'activeTab' => 'session'])

<div class="space-y-6">
    <!-- Modern Header -->
    <div class="bg-white border-b border-slate-200 -mx-4 lg:-mx-8 -mt-4 lg:-mt-8 px-4 lg:px-8 py-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col gap-4">
                <h1 class="text-3xl font-bold text-slate-800">{{ $classroom->course->name }}</h1>
                <div class="flex flex-wrap items-center gap-6 text-sm text-slate-500">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        {{ $classroom->course->code }}
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        Credit {{ $classroom->course->credits }}/0
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m4 0h1m-5 10h1m4 0h1m-5-4h1m4 0h1"/></svg>
                        {{ $classroom->room ?? 'TBA' }} - {{ $classroom->type }}
                    </div>
                </div>

                <!-- Instructor Info -->
                <div class="flex items-center gap-4 mt-2">
                    <div class="w-12 h-12 rounded-full bg-slate-200 border-2 border-slate-100 flex items-center justify-center text-slate-600 font-bold overflow-hidden">
                         @if($classroom->lecturers->count() > 0)
                            {{ substr($classroom->lecturers->first()->name, 0, 1) }}
                         @else
                            <svg class="w-6 h-6 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                         @endif
                    </div>
                    <div>
                        <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest leading-none">Primary Instructor</p>
                        <p class="text-sm font-bold text-slate-700 mt-1">
                            @if($classroom->lecturers->count() > 0)
                                D6365 - {{ strtoupper($classroom->lecturers->first()->name) }}
                            @else
                                Not Assigned
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Tabs -->
    <div class="flex items-center gap-8 border-b border-slate-200 overflow-x-auto no-scrollbar">
        <a href="{{ route('class.show', $classroom) }}" class="py-3 border-b-2 {{ $activeTab === 'session' ? 'border-orange-500 text-orange-500 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800 font-medium' }} text-sm whitespace-nowrap transition-all">Session</a>
        <a href="{{ route('syllabus.show', $classroom) }}" class="py-3 border-b-2 {{ $activeTab === 'syllabus' ? 'border-orange-500 text-orange-500 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800 font-medium' }} text-sm whitespace-nowrap transition-all">Syllabus</a>
        <a href="{{ route('forum.index', $classroom) }}" class="py-3 border-b-2 {{ $activeTab === 'forum' ? 'border-orange-500 text-orange-500 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800 font-medium' }} text-sm whitespace-nowrap transition-all">Forum</a>
        <a href="#" class="py-3 border-b-2 {{ $activeTab === 'assessment' ? 'border-orange-500 text-orange-500 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800 font-medium' }} text-sm whitespace-nowrap transition-all">Assessment</a>
        <a href="{{ route('gradebook.index', $classroom) }}" class="py-3 border-b-2 {{ $activeTab === 'gradebook' ? 'border-orange-500 text-orange-500 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800 font-medium' }} text-sm whitespace-nowrap transition-all">Gradebook</a>
        <a href="{{ route('class.people', $classroom) }}" class="py-3 border-b-2 {{ $activeTab === 'people' ? 'border-orange-500 text-orange-500 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800 font-medium' }} text-sm whitespace-nowrap transition-all">People</a>
    </div>
</div>
