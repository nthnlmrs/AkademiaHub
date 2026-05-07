@section('title', 'Create New Thread - ' . $classroom->course->name)

<x-app-layout>
    <div class="space-y-6 max-w-5xl mx-auto">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-800">Create New Thread</h2>
            <a href="{{ route('forum.index', $classroom) }}" class="text-sm text-slate-500 hover:text-orange-500 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Cancel
            </a>
        </div>

        <form action="{{ route('forum.store', $classroom) }}" method="POST" class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            @csrf
            
            <!-- Course & Session Header -->
            <div class="px-8 py-6 bg-slate-50/50 border-b border-slate-100 flex justify-between items-start gap-8">
                <div class="space-y-1">
                    <label class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Course</label>
                    <p class="font-semibold text-slate-700">{{ $classroom->course->code }} - {{ $classroom->type }} - {{ $classroom->course->name }}</p>
                </div>
                <div class="w-64 space-y-1">
                    <label class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Session</label>
                    <select name="course_session_id" required class="w-full bg-white border-slate-200 rounded-xl text-sm focus:ring-orange-500 focus:border-orange-500 py-2.5">
                        <option value="" disabled selected>Select Session</option>
                        @foreach($sessions as $s)
                            <option value="{{ $s->id }}">Session {{ $s->session_number }} - {{ $s->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Thread Content -->
            <div class="p-8 space-y-6">
                <div class="space-y-1">
                    <label class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Thread</label>
                    <div class="flex items-center gap-3 py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">To:</span>
                        <span class="px-3 py-1 bg-slate-50 border border-slate-200 rounded-full text-xs font-bold text-slate-600">{{ $classroom->name }}</span>
                    </div>
                </div>

                <div class="space-y-4">
                    <input type="text" name="title" required placeholder="Write title here..." 
                           class="w-full border-none p-0 text-xl font-medium placeholder-slate-300 focus:ring-0">
                    
                    <div class="space-y-4">
                        <textarea name="body" required rows="14" placeholder="Write something inspiring..." 
                                  class="w-full border-none p-0 text-slate-600 placeholder-slate-300 focus:ring-0 leading-relaxed"></textarea>
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="px-8 py-4 bg-slate-50/30 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-10 py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-lg shadow-lg shadow-orange-100 transition-all uppercase tracking-widest text-sm">
                    Post
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
