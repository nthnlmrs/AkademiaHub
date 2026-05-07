@section('title', 'Edit Course')

<x-app-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin.courses.index') }}" class="text-sm text-slate-500 hover:text-indigo-400 transition-colors">← Back to Courses</a>
            <h2 class="text-2xl font-bold mt-2">Edit Course: {{ $course->name }}</h2>
        </div>

        <div class="glass-card p-6">
            <form method="POST" action="{{ route('admin.courses.update', $course) }}" class="space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="form-label">Course Code</label>
                    <input type="text" name="code" value="{{ old('code', $course->code) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Course Name</label>
                    <input type="text" name="name" value="{{ old('name', $course->name) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Credits</label>
                    <input type="number" name="credits" value="{{ old('credits', $course->credits) }}" class="form-input" required>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary">Update Course</button>
                    <a href="{{ route('admin.courses.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
