@section('title', 'Create Course')

<x-app-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin.courses.index') }}" class="text-sm text-slate-500 hover:text-indigo-400 transition-colors">← Back to Courses</a>
            <h2 class="text-2xl font-bold mt-2">Create Course</h2>
        </div>

        <div class="glass-card p-6">
            <form method="POST" action="{{ route('admin.courses.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="form-label">Course Code</label>
                    <input type="text" name="code" value="{{ old('code') }}" class="form-input" required placeholder="CS101">
                </div>
                <div>
                    <label class="form-label">Course Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input" required placeholder="Introduction to Computer Science">
                </div>
                <div>
                    <label class="form-label">Credits</label>
                    <input type="number" name="credits" value="{{ old('credits', 4) }}" class="form-input" required placeholder="4">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary">Create Course</button>
                    <a href="{{ route('admin.courses.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
