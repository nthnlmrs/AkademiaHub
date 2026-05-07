@section('title', 'Manage Sessions - ' . $classroom->course->name)

<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.classrooms.index') }}" class="text-sm text-slate-500 hover:text-indigo-400 transition-colors">← Back to Classes</a>
                <h2 class="text-2xl font-bold mt-2">{{ $classroom->course->name }} - Class {{ $classroom->name }} ({{ $classroom->type }})</h2>
                <p class="text-slate-500 mt-1">Manage sessions for this class</p>
            </div>
            <a href="{{ route('admin.classrooms.sessions.create', $classroom) }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Session
            </a>
        </div>

        <div class="space-y-3">
            @forelse($classroom->courseSessions as $session)
                <div class="glass-card p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-indigo-500/15 flex items-center justify-center">
                                <span class="text-lg font-bold text-indigo-400">{{ $session->session_number }}</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-900">{{ $session->title }}</h4>
                                <p class="text-sm text-slate-500">Session {{ $session->session_number }}{{ $session->description ? ' • ' . \Illuminate\Support\Str::limit($session->description, 80) : '' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.classrooms.sessions.edit', [$classroom, $session]) }}" class="text-indigo-400 hover:text-indigo-300 text-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.classrooms.sessions.destroy', [$classroom, $session]) }}" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="glass-card p-8 text-center text-slate-500">
                    <p>No sessions yet. Create the first session.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
