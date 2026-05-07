@section('title', 'Create User')

<x-app-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-500 hover:text-indigo-400 transition-colors">← Back to Users</a>
            <h2 class="text-2xl font-bold mt-2">Create User</h2>
        </div>

        <div class="glass-card p-6">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input" required placeholder="John Doe">
                </div>

                <div>
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input" required placeholder="john@example.com">
                </div>

                <div>
                    <label class="form-label">NIM / NIP</label>
                    <input type="text" name="nim_nip" value="{{ old('nim_nip') }}" class="form-input" required placeholder="Student/Lecturer ID">
                </div>

                <div>
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" required placeholder="Min. 8 characters">
                </div>

                <div>
                    <label class="form-label">Role</label>
                    <select name="role" id="roleSelect" class="form-select" required onchange="toggleStudentType()">
                        <option value="">Select Role</option>
                        <option value="lecturer" {{ old('role') === 'lecturer' ? 'selected' : '' }}>Lecturer</option>
                        <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
                    </select>
                </div>

                <div id="studentTypeField" class="{{ old('role') === 'student' ? '' : 'hidden' }}">
                    <label class="form-label">Student Type</label>
                    <select name="student_type" class="form-select">
                        <option value="regular" {{ old('student_type') === 'regular' ? 'selected' : '' }}>Regular Student</option>
                        <option value="teaching_assistant" {{ old('student_type') === 'teaching_assistant' ? 'selected' : '' }}>Teaching Assistant</option>
                    </select>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary">Create User</button>
                    <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleStudentType() {
            const role = document.getElementById('roleSelect').value;
            const field = document.getElementById('studentTypeField');
            field.classList.toggle('hidden', role !== 'student');
        }
    </script>
</x-app-layout>
