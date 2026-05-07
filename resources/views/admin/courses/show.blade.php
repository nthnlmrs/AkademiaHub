@section('title', 'Course Sessions - ' . $course->name)

<x-app-layout>
    <div x-data="{ editModalOpen: false, activityModalOpen: false, currentSession: {} }" class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-slate-800">{{ $course->code }} - {{ $course->name }}</h2>
            <a href="{{ route('admin.courses.index') }}" class="text-sm text-slate-500 hover:text-indigo-600 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Courses
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Add Session Form -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 h-fit">
                <h3 class="text-lg font-bold text-slate-800 mb-6">Add New Session</h3>
                <form action="{{ route('admin.courses.sessions.store', $course) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Session Number</label>
                        <input type="number" name="session_number" required value="{{ $course->courseSessions->count() + 1 }}" 
                               class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Session Title</label>
                        <input type="text" name="title" required placeholder="e.g. Introduction to Calculus" 
                               class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Description / Learning Outcome</label>
                        <textarea name="description" rows="5" placeholder="First line as Learning Outcome, subsequent lines as Sub Topics..." 
                                  class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>
                    <button type="submit" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all uppercase tracking-widest text-sm">
                        Add Session
                    </button>
                </form>
            </div>

            <!-- Sessions List -->
            <div class="lg:col-span-2 space-y-6">
                <h3 class="text-xl font-bold text-slate-800 mb-2">Curriculum</h3>
                @forelse($course->courseSessions as $session)
                    <div class="bg-white border border-slate-200 rounded-3xl p-8 space-y-6">
                        <div class="flex justify-between items-start">
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <span class="px-2 py-1 bg-indigo-50 text-indigo-600 text-[0.65rem] font-bold rounded uppercase">Session {{ $session->session_number }}</span>
                                    <h4 class="font-bold text-slate-800 text-xl">{{ $session->title }}</h4>
                                </div>
                                <p class="text-sm text-slate-500 whitespace-pre-line">{{ $session->description }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="currentSession = {{ json_encode($session) }}; editModalOpen = true" class="p-2 text-slate-400 hover:text-indigo-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route('admin.courses.sessions.destroy', [$course, $session]) }}" method="POST" onsubmit="return confirm('Delete this session?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Activities (Things to do) -->
                        <div class="pt-6 border-t border-slate-50">
                            <div class="flex items-center justify-between mb-4">
                                <h5 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Things to do</h5>
                                <button @click="currentSession = {{ json_encode($session) }}; activityModalOpen = true" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    Add Activity
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @forelse($session->activities as $activity)
                                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl group/act">
                                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-indigo-500 shadow-sm">
                                            @if($activity->type === 'attendance')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
                                            @elseif($activity->type === 'video')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            @elseif($activity->type === 'file')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-slate-700 truncate">{{ $activity->title }}</p>
                                            @if($activity->duration)
                                                <p class="text-[0.65rem] text-slate-400">{{ $activity->duration }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full py-4 text-center border border-dashed border-slate-200 rounded-2xl">
                                        <p class="text-[0.65rem] text-slate-400 italic">No activities added.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center border-2 border-dashed border-slate-200 rounded-3xl">
                        <p class="text-slate-400 italic">No sessions defined for this course yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Add Activity Modal -->
        <div x-show="activityModalOpen" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="activityModalOpen = false"></div>
                
                <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 border border-slate-100"
                     x-data="{ actType: 'file' }">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-slate-800">Add New Activity</h3>
                        <button @click="activityModalOpen = false" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    
                    <form :action="`{{ url('sessions') }}/${currentSession.id}/activities`" method="POST" class="space-y-4" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Activity Type</label>
                            <select name="type" x-model="actType" required class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="file">Course Material (Document)</option>
                                <option value="link">External Link</option>
                                <option value="video">Video</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Activity Title</label>
                            <input type="text" name="title" required placeholder="e.g. Session 1 Lecture Notes" 
                                   class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        {{-- Dynamic field: Document type → file upload --}}
                        <div x-show="actType === 'file'">
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Upload Document</label>
                            <label class="flex items-center gap-4 p-4 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition-all group">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-slate-400 group-hover:text-indigo-500 shadow-sm transition-colors">
                                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                </div>
                                <div class="flex-1">
                                    <span class="block text-xs font-bold text-slate-600">Choose file...</span>
                                    <span class="block text-[0.65rem] text-slate-400">PDF, DOC, PPT, ZIP (Max 20MB)</span>
                                </div>
                                <input type="file" name="document" class="hidden" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip">
                            </label>
                        </div>

                        {{-- Dynamic field: Video → video URL --}}
                        <div x-show="actType === 'video'">
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Video URL</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">https://</span>
                                <input type="text" name="url" placeholder="youtube.com/watch?v=..." 
                                       class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 pl-16">
                            </div>
                            <p class="text-[0.65rem] text-slate-400 mt-1 italic">Paste YouTube, Vimeo, or any video link.</p>
                        </div>

                        {{-- Dynamic field: External Link --}}
                        <div x-show="actType === 'link'">
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">External Link</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">https://</span>
                                <input type="text" name="url" placeholder="example.com" 
                                       class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 pl-16">
                            </div>
                            <p class="text-[0.65rem] text-slate-400 mt-1 italic">Enter URL without https://</p>
                        </div>
                        
                        <div class="pt-4">
                            <button type="submit" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all uppercase tracking-widest text-sm">
                                Create Activity
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Session Modal -->
        <div x-show="editModalOpen" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="editModalOpen = false"></div>
                
                <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 border border-slate-100">
                    <h3 class="text-xl font-bold text-slate-800 mb-6">Edit Session</h3>
                    
                    <form :action="`{{ url('admin/courses/' . $course->id . '/sessions') }}/${currentSession.id}`" method="POST" class="space-y-4">
                        @csrf @method('PUT')
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Session Number</label>
                            <input type="number" name="session_number" :value="currentSession.session_number" required
                                   class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Session Title</label>
                            <input type="text" name="title" :value="currentSession.title" required
                                   class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Description</label>
                            <textarea name="description" rows="5" x-text="currentSession.description"
                                      class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        
                        <div class="flex gap-3 pt-4">
                            <button type="submit" class="flex-1 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all uppercase tracking-widest text-sm">
                                Save Changes
                            </button>
                            <button type="button" @click="editModalOpen = false" class="px-6 py-3 bg-slate-100 text-slate-500 font-bold rounded-xl hover:bg-slate-200 transition-all uppercase tracking-widest text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
