<x-app-layout>
    <div class="space-y-6">
        <x-class-header :classroom="$classroom" activeTab="session" />

        <!-- Session Selector Horizontal -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-2">
            @foreach($sessions as $s)
                <a href="{{ route('class.session', [$classroom, $s]) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap relative
                   {{ $activeSession && $activeSession->id === $s->id 
                      ? 'bg-orange-500 text-white shadow-md' 
                      : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}">
                    Session {{ $s->session_number }}
                    @if($loop->index > 2 && $loop->index < 10) <!-- Small orange dot like in screenshot -->
                        <span class="absolute top-1 right-1 w-1.5 h-1.5 bg-orange-400 rounded-full"></span>
                    @endif
                </a>
            @endforeach
            <button class="px-4 py-2 bg-slate-200 text-slate-600 rounded-lg text-sm font-semibold hover:bg-slate-300 flex items-center gap-1">
                More <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
        </div>

        <!-- Session Content Section -->
        @if($activeSession)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 mt-8">
                <!-- Session Details (Left) -->
                <div class="lg:col-span-2 space-y-10">
                    <div>
                        <h2 class="text-4xl font-bold text-slate-800">{{ $activeSession->title }}</h2>
                        
                        <div class="mt-10 space-y-8">
                            @php
                                // Parsing description for Learning Outcome and Sub Topic
                                $parts = explode("\n", $activeSession->description);
                                $learningOutcome = $parts[0] ?? '';
                                $subTopics = array_slice($parts, 1);
                            @endphp

                            <div class="space-y-4">
                                <h3 class="text-xl font-bold text-slate-900 border-l-4 border-cyan-400 pl-4">Learning Outcome</h3>
                                <p class="text-slate-600 leading-relaxed pl-5">{{ $learningOutcome }}</p>
                            </div>

                            <div class="space-y-4">
                                <h3 class="text-xl font-bold text-slate-900 border-l-4 border-cyan-400 pl-4">Sub Topic</h3>
                                <ul class="space-y-3 pl-5">
                                    @foreach($subTopics as $topic)
                                        @if(trim($topic))
                                            <li class="flex gap-3 items-center">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300 flex-shrink-0"></span>
                                                <p class="text-slate-600">{{ trim($topic, "- \t\n\r\0\x0B") }}</p>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar: Things to do in this session -->
                <div class="space-y-4">
                    <div class="bg-orange-500 rounded-3xl p-8 shadow-lg shadow-orange-100 text-white min-h-[400px]">
                        <h3 class="text-lg font-bold mb-8 border-b border-white/20 pb-4">Things to do in this session</h3>
                        
                        <div class="space-y-6">
                            @forelse($activeSession->activities as $activity)
                                <div class="group/item relative">
                                    @if($activity->url)
                                        <a href="{{ $activity->url }}" target="_blank" class="flex items-center gap-4 hover:bg-white/10 p-3 -m-3 rounded-2xl transition-all">
                                    @else
                                        <div class="flex items-center gap-4 p-3 -m-3">
                                    @endif
                                        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center group-hover/item:bg-white/30 transition-colors">
                                            @if($activity->type === 'video')
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            @elseif($activity->type === 'file')
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            @else
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-bold text-sm truncate">{{ $activity->title }}</p>
                                            <p class="text-[0.65rem] text-white/60 font-medium uppercase tracking-wider">
                                                {{ $activity->type === 'file' ? 'Course Material' : ($activity->type === 'video' ? 'Video Content' : 'External Link') }}
                                            </p>
                                        </div>
                                        @if($activity->url)
                                            <div class="text-white/40 group-hover/item:text-white transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </div>
                                        @endif
                                    @if($activity->url)
                                        </a>
                                    @else
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-10 text-white/40 border border-white/20 rounded-2xl">
                                    <p class="text-xs italic">No activities.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Admin: Add Activity Button -->
                        @if(Auth::user()->isAdmin() || Auth::user()->role === 'lecturer' || Auth::user()->role === 'admin_lab')
                            <button @click="$dispatch('open-add-activity-modal')" class="mt-10 w-full py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Add Activity
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="py-20 text-center border-2 border-dashed border-slate-200 rounded-3xl">
                <p class="text-slate-400 italic font-medium">No sessions available.</p>
            </div>
        @endif
    </div>

    <!-- Modal for Adding Activity (Admin/Lecturer Only) -->
    @if(Auth::user()->isAdmin() || Auth::user()->role === 'lecturer' || Auth::user()->role === 'admin_lab')
        <div x-data="{ open: false }" @open-add-activity-modal.window="open = true" 
             x-show="open" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="open = false"></div>
                <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 transform transition-all overflow-hidden border border-slate-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-extrabold text-slate-800 tracking-tight">Add Session Activity</h3>
                        <button @click="open = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    
                    <form action="{{ route('session_activities.store', $activeSession) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Activity Type</label>
                            <select name="type" required class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="file">Course Material (Document)</option>
                                <option value="link">External Link</option>
                                <option value="video">Video</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Activity Title</label>
                            <input type="text" name="title" required placeholder="e.g. Session 1 Lecture Notes" class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Link / URL</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">https://</span>
                                <input type="text" name="url" placeholder="google.com" 
                                       class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 pl-16">
                            </div>
                            <p class="text-[0.65rem] text-slate-400 mt-1 italic">Enter link without https://</p>
                        </div>
                        <button type="submit" class="w-full py-4 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all mt-4">
                            Save Activity
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
