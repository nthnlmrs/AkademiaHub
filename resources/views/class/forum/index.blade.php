@section('title', 'Forum - ' . $classroom->course->name)

<x-app-layout>
    <div class="space-y-6">
        <x-class-header :classroom="$classroom" activeTab="forum" />

        <!-- Forum Toolbar -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-200">
            <a href="{{ route('forum.create', $classroom) }}" class="px-8 py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-lg shadow-lg shadow-orange-100 transition-all uppercase tracking-wide text-sm">
                Create New Thread
            </a>
            
            <div class="flex items-center gap-6 text-sm text-slate-500">
                <span>{{ $posts->total() }} Result(s)</span>
                <div class="flex items-center gap-2">
                    <span>Show:</span>
                    <select class="bg-white border-slate-200 rounded-lg text-xs focus:ring-orange-500 focus:border-orange-500 py-1">
                        <option>10</option>
                        <option>20</option>
                        <option>50</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Posts List -->
        <div class="space-y-4 mt-8">
            @forelse($posts as $post)
                <div class="bg-white border border-slate-200 rounded-2xl p-6 transition-all hover:border-orange-200">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold">
                                {{ strtoupper(substr($post->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-lg group-hover:text-orange-500 transition-colors">
                                    {{ $post->title }}
                                </h4>
                                <div class="flex items-center gap-2 mt-1 text-xs text-slate-500 font-medium">
                                    <span class="text-orange-500">{{ $post->user->name }}</span>
                                    <span>&middot;</span>
                                    <span>{{ $post->created_at->diffForHumans() }}</span>
                                    @if($post->courseSession)
                                        <span>&middot;</span>
                                        <span class="bg-slate-100 px-2 py-0.5 rounded text-slate-600">Session {{ $post->courseSession->session_number }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        @if(Auth::user()->isAdmin() || Auth::user()->id === $post->user_id)
                            <form method="POST" action="{{ route('forum.destroy', $post) }}" onsubmit="return confirm('Delete this thread?')">
                                @csrf @method('DELETE')
                                <button class="text-slate-300 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        @endif
                    </div>
                    
                    <p class="text-slate-600 text-sm line-clamp-2">{{ strip_tags($post->body) }}</p>
                    
                    <div class="mt-4 pt-4 border-t border-slate-50 flex items-center gap-4">
                        <button class="text-xs font-bold text-slate-400 hover:text-orange-500 flex items-center gap-1 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            {{ $post->replies->count() }} Replies
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center border-2 border-dashed border-slate-200 rounded-3xl">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">No discussions yet</h3>
                    <p class="text-slate-500 text-sm mt-1">Be the first to start a conversation in this class.</p>
                </div>
            @endforelse

            <div class="pt-6">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
