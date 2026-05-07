@section('title', 'Edit Lab Class')

<x-app-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin_lab.classes.index') }}" class="text-sm text-slate-500 hover:text-indigo-400 transition-colors">← Back to Lab Classes</a>
            <h2 class="text-2xl font-bold mt-2">Edit Lab Class: {{ $classroom->course->name }} - {{ $classroom->name }}</h2>
        </div>

        <div class="glass-card p-6">
            <form method="POST" action="{{ route('admin_lab.classes.update', $classroom) }}" class="space-y-5">
                @csrf @method('PUT')

                <div>
                    <label class="form-label">Course</label>
                    <select name="course_id" class="form-select" required>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $classroom->course_id) == $course->id ? 'selected' : '' }}>
                                {{ $course->code }} - {{ $course->name }}
                            </option>
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

                <div>
                    <label class="form-label">Mode</label>
                    <select name="mode" class="form-select" required>
                        <option value="onsite" {{ old('mode', $classroom->mode) === 'onsite' ? 'selected' : '' }}>Onsite</option>
                        <option value="online" {{ old('mode', $classroom->mode) === 'online' ? 'selected' : '' }}>Online</option>
                    </select>
                </div>

                <div class="bg-cyan-500/10 border border-cyan-500/20 rounded-xl p-3 flex items-center gap-3">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    <span class="text-sm text-cyan-300">Class type: <strong>LAB</strong> (fixed)</span>
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
                    <label class="form-label">
                        Assign Teaching Assistants
                        <span class="text-cyan-400 text-xs font-normal ml-1">(TA only)</span>
                    </label>
                    <div class="select-list">
                        @foreach($teachingAssistants as $ta)
                            <label>
                                <input type="checkbox" name="teaching_assistants[]" value="{{ $ta->id }}" class="form-checkbox"
                                    {{ in_array($ta->id, old('teaching_assistants', $selectedTAs)) ? 'checked' : '' }}>
                                <span>{{ $ta->name }}</span>
                                <span class="text-cyan-400 text-xs font-mono ml-1">{{ $ta->ta_id }}</span>
                                <span class="text-slate-500 text-xs ml-auto">{{ $ta->nim_nip }}</span>
                            </label>
                        @endforeach
                        @if($teachingAssistants->isEmpty())
                            <p class="text-sm text-slate-500 p-2">No TAs available.</p>
                        @endif
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
                                @if($student->isTeachingAssistant())
                                    <span class="text-cyan-400 text-xs ml-1">(TA: {{ $student->ta_id }})</span>
                                @endif
                                <span class="text-slate-500 text-xs ml-auto">{{ $student->nim_nip }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary">Update Lab Class</button>
                    <a href="{{ route('admin_lab.classes.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
