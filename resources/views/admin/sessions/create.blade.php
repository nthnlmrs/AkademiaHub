@section('title', 'Create Session')

<x-app-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin.classrooms.sessions.index', $classroom) }}" class="text-sm text-slate-500 hover:text-indigo-400 transition-colors">← Back to Sessions</a>
            <h2 class="text-2xl font-bold mt-2">Create Session</h2>
            <p class="text-slate-500 mt-1">{{ $classroom->course->name }} - Class {{ $classroom->name }}</p>
        </div>

        <div class="glass-card p-6">
            <form method="POST" action="{{ route('admin.classrooms.sessions.store', $classroom) }}" class="space-y-5">
                @csrf
                <div>
                    <label class="form-label">Session Number</label>
                    <input type="number" name="session_number" value="{{ old('session_number', $nextNumber) }}" class="form-input" required min="1">
                </div>
                <div>
                    <label class="form-label">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="form-input" required placeholder="Introduction to the Course">
                </div>
                <div>
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea" placeholder="Session description...">{{ old('description') }}</textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary">Create Session</button>
                    <a href="{{ route('admin.classrooms.sessions.index', $classroom) }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
