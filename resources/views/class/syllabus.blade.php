@section('title', 'Syllabus - ' . $classroom->course->name)

<x-app-layout>
    <div class="space-y-6">
        <x-class-header :classroom="$classroom" activeTab="syllabus" />

        @if($syllabus)
            <div class="bg-white border border-slate-200 rounded-2xl p-8">
                @if($syllabus->content)
                    <div class="prose prose-invert max-w-none text-slate-900">
                        {!! nl2br(e($syllabus->content)) !!}
                    </div>
                @endif

                @if($syllabus->file_path)
                    <div class="mt-4 pt-4 border-t border-slate-200/50">
                        <div class="module-card">
                            <span class="text-2xl">📄</span>
                            <div class="flex-1">
                                <p class="font-medium text-slate-900">{{ $syllabus->file_name }}</p>
                                <p class="text-xs text-slate-500">Syllabus document</p>
                            </div>
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($syllabus->file_path) }}" target="_blank" class="btn-secondary text-xs py-1 px-3">Download</a>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="glass-card p-8 text-center text-slate-500">
                <p>No syllabus available yet.</p>
            </div>
        @endif

        @if(Auth::user()->isAdmin() || Auth::user()->isLecturer())
            <div class="bg-white border border-slate-200 rounded-2xl p-8">
                <h3 class="text-sm font-semibold text-slate-600 mb-3">{{ $syllabus ? 'Update' : 'Create' }} Syllabus</h3>
                <form method="POST" action="{{ route('syllabus.store', $classroom) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="form-label">Syllabus Content</label>
                        <textarea name="content" class="form-textarea" rows="10" placeholder="Write syllabus content...">{{ old('content', $syllabus->content ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">Upload Syllabus File (optional)</label>
                        <input type="file" name="file" class="form-input">
                    </div>
                    <button type="submit" class="btn-primary">Save Syllabus</button>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>
