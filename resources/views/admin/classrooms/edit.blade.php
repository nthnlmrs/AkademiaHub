@section('title', 'Edit Class')

<x-app-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin.classrooms.index') }}" class="text-sm text-slate-500 hover:text-indigo-400 transition-colors">← Back to Classes</a>
            <h2 class="text-2xl font-bold mt-2">Edit Class: {{ $classroom->course->name }} - {{ $classroom->name }}</h2>
        </div>

        <div class="glass-card p-6">
            <form method="POST" action="{{ route('admin.classrooms.update', $classroom) }}" class="space-y-5">
                @csrf @method('PUT')

                <div>
                    <label class="form-label">Course</label>
                    <select name="course_id" class="form-select" required>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $classroom->course_id) == $course->id ? 'selected' : '' }}>{{ $course->code }} - {{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Class Name</label>
                        <input type="text" name="name" value="{{ old('name', $classroom->name) }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Room</label>
                        <input type="text" name="room" value="{{ old('room', $classroom->room) }}" class="form-input">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="LEC" {{ old('type', $classroom->type) === 'LEC' ? 'selected' : '' }}>Lecture (LEC)</option>
                            <option value="LAB" {{ old('type', $classroom->type) === 'LAB' ? 'selected' : '' }}>Laboratory (LAB)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Mode</label>
                        <select name="mode" class="form-select" required>
                            <option value="onsite" {{ old('mode', $classroom->mode) === 'onsite' ? 'selected' : '' }}>Onsite</option>
                            <option value="online" {{ old('mode', $classroom->mode) === 'online' ? 'selected' : '' }}>Online</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label">Assign Lecturers</label>
                    <div class="select-list">
                        @foreach($lecturers as $lecturer)
                            <label>
                                <input type="checkbox" name="lecturers[]" value="{{ $lecturer->id }}" class="form-checkbox"
                                    {{ in_array($lecturer->id, old('lecturers', $selectedLecturers)) ? 'checked' : '' }}>
                                <span>{{ $lecturer->name }}</span>
                                <span class="text-slate-500 text-xs ml-auto">{{ $lecturer->nim_nip }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="form-label">Assign Students</label>
                    <div class="select-list" style="max-height: 300px">
                        @foreach($students as $student)
                            <label>
                                <input type="checkbox" name="students[]" value="{{ $student->id }}" class="form-checkbox"
                                    {{ in_array($student->id, old('students', $selectedStudents)) ? 'checked' : '' }}>
                                <span>{{ $student->name }}</span>
                                <span class="text-xs ml-1 {{ $student->student_type === 'teaching_assistant' ? 'text-cyan-400' : 'text-slate-500' }}">
                                    {{ $student->student_type === 'teaching_assistant' ? '(TA)' : '' }}
                                </span>
                                <span class="text-slate-500 text-xs ml-auto">{{ $student->nim_nip }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary">Update Class</button>
                    <a href="{{ route('admin.classrooms.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
