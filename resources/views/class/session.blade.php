@section('title', 'Session ' . $session->session_number . ' - ' . $classroom->course->name)

<x-app-layout>
    <div class="space-y-6">
        <!-- Breadcrumb -->
        <div>
            <a href="{{ route('class.show', $classroom) }}" class="text-sm text-slate-500 hover:text-indigo-400 transition-colors">← {{ $classroom->course->name }} - Class {{ $classroom->name }}</a>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Session Navigation Sidebar -->
            <div class="lg:w-64 shrink-0">
                <div class="glass-card p-4 lg:sticky lg:top-24">
                    <h4 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Sessions</h4>
                    <div class="space-y-1">
                        @foreach($allSessions as $s)
                            <a href="{{ route('class.session', [$classroom, $s]) }}" class="session-nav-item {{ $s->id === $session->id ? 'active' : '' }}">
                                <span class="w-6 h-6 rounded-md text-xs font-bold flex items-center justify-center {{ $s->id === $session->id ? 'bg-indigo-500/20 text-indigo-400' : 'bg-white text-slate-500' }}">{{ $s->session_number }}</span>
                                <span class="truncate">{{ $s->title }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 space-y-6">
                <!-- Session Header -->
                <div class="glass-card p-6">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="badge badge-primary">Session {{ $session->session_number }}</span>
                        <span class="badge {{ $classroom->type === 'LAB' ? 'badge-info' : 'badge-primary' }}">{{ $classroom->type }}</span>
                    </div>
                    <h2 class="text-xl font-bold">{{ $session->title }}</h2>
                    @if($session->description)
                        <p class="text-slate-600 mt-2">{{ $session->description }}</p>
                    @endif
                    <div class="flex items-center gap-4 mt-3 text-sm text-slate-500">
                        <span>Lecturer: {{ $classroom->lecturers->pluck('name')->join(', ') ?: 'TBA' }}</span>
                        @if($classroom->type === 'LAB' && $classroom->teachingAssistants->count() > 0)
                            <span class="text-cyan-400">TA: {{ $classroom->teachingAssistants->pluck('name')->join(', ') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Modules -->
                <div class="glass-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Learning Materials
                        </h3>
                    </div>

                    @if($session->modules->count() > 0)
                        <div class="space-y-2 mb-4">
                            @foreach($session->modules as $module)
                                <div class="module-card">
                                    <span class="text-2xl">{{ $module->file_icon }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-slate-900 truncate">{{ $module->title }}</p>
                                        <p class="text-xs text-slate-500">{{ $module->file_name }} &middot; {{ $module->file_size_formatted }} &middot; by {{ $module->uploader->name }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('module.download', $module) }}" class="btn-secondary text-xs py-1 px-3">Download</a>
                                        @if(Auth::user()->isAdmin() || Auth::user()->id === $module->uploaded_by)
                                            <form method="POST" action="{{ route('module.destroy', $module) }}" onsubmit="return confirm('Delete?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-300 text-xs">✕</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-slate-500 text-sm mb-4">No materials uploaded yet.</p>
                    @endif

                    @if(Auth::user()->isAdmin() || Auth::user()->isLecturer() || Auth::user()->isTeachingAssistant())
                        <form method="POST" action="{{ route('module.store', [$classroom, $session]) }}" enctype="multipart/form-data" class="border-t border-slate-200/50 pt-4 mt-4">
                            @csrf
                            <h4 class="text-sm font-semibold text-slate-600 mb-3">Upload New Material</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <input type="text" name="title" class="form-input" required placeholder="Material title">
                                <input type="file" name="file" class="form-input" required>
                            </div>
                            <button type="submit" class="btn-primary mt-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                Upload
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Quick Links -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <a href="{{ route('forum.index', $classroom) }}" class="glass-card p-4 text-center hover:border-indigo-500/30 transition-all">
                        <svg class="w-6 h-6 mx-auto mb-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span class="text-sm font-medium">Forum</span>
                    </a>
                    <a href="{{ route('gradebook.index', $classroom) }}" class="glass-card p-4 text-center hover:border-indigo-500/30 transition-all">
                        <svg class="w-6 h-6 mx-auto mb-2 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span class="text-sm font-medium">Gradebook</span>
                    </a>
                    <a href="{{ route('syllabus.show', $classroom) }}" class="glass-card p-4 text-center hover:border-indigo-500/30 transition-all">
                        <svg class="w-6 h-6 mx-auto mb-2 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="text-sm font-medium">Syllabus</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
