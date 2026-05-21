@section('title', 'Edit Session')

<x-app-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin.classrooms.sessions.index', $classroom) }}" class="text-sm text-slate-500 hover:text-indigo-400 transition-colors">← Back to Sessions</a>
            <h2 class="text-2xl font-bold mt-2">Edit Session {{ $session->session_number }}</h2>
        </div>

        <div class="glass-card p-6">
            <form method="POST" action="{{ route('admin.classrooms.sessions.update', [$classroom, $session]) }}" class="space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="form-label">Session Number</label>
                    <input type="number" name="session_number" value="{{ old('session_number', $session->session_number) }}" class="form-input" required min="1">
                </div>
                <div>
                    <label class="form-label">Title</label>
                    <input type="text" name="title" value="{{ old('title', $session->title) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea">{{ old('description', $session->description) }}</textarea>
                </div>
                <div>
                    <label class="form-label">Interactive Text (SLB Reader)</label>
                    <textarea name="interactive_text" class="form-textarea h-32" placeholder="Masukkan teks interaktif khusus untuk fitur Interactive Text Reader (opsional)...">{{ old('interactive_text', $session->interactive_text) }}</textarea>
                    <p class="text-xs text-slate-500 mt-1">Teks ini akan dibacakan per kata saat diklik pada fitur Interactive Text Reader.</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary">Update Session</button>
                    <a href="{{ route('admin.classrooms.sessions.index', $classroom) }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
