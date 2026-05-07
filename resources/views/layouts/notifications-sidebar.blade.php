<div x-data class="relative z-50">
    <!-- Overlay -->
    <div x-show="$store.notifications.open" 
         x-transition:enter="transition-opacity ease-linear duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition-opacity ease-linear duration-300" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$store.notifications.close()"></div>

    <!-- Slide-over panel -->
    <div x-show="$store.notifications.open" 
         x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500" 
         x-transition:enter-start="translate-x-full" 
         x-transition:enter-end="translate-x-0" 
         x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500" 
         x-transition:leave-start="translate-x-0" 
         x-transition:leave-end="translate-x-full" 
         @keydown.escape.window="$store.notifications.close()"
         class="fixed inset-y-0 right-0 w-full max-w-sm bg-slate-50 shadow-2xl flex flex-col pointer-events-auto border-l border-slate-200">
         
         <!-- Header -->
         <div class="flex items-center justify-between px-5 pt-6 pb-4 bg-white">
             <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Notifications</h2>
             <button @click="$store.notifications.close()" class="p-2 -mr-2 text-slate-400 hover:text-slate-600 transition-colors rounded-full hover:bg-slate-100">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
             </button>
         </div>

         <!-- Actions -->
         <div class="px-5 py-4 flex justify-between items-center bg-slate-50 border-b border-slate-200">
             <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Recent Activity</span>
             @if(Auth::user()->unreadNotifications->count() > 0)
                 <form method="POST" action="{{ route('notifications.markRead') }}">
                     @csrf
                     <button type="submit" class="flex items-center gap-1.5 text-sm text-sky-500 hover:text-sky-600 font-medium">
                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                         Mark All Read
                     </button>
                 </form>
             @else
                 <span class="text-sm text-slate-400 flex items-center gap-1.5 opacity-60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Mark All Read
                 </span>
             @endif
         </div>

         <!-- Notification List -->
         <div class="flex-1 overflow-y-auto px-4 pb-6 space-y-3">
            @forelse(Auth::user()->notifications()->latest()->take(20)->get() as $notif)
                <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm relative overflow-hidden transition-all hover:border-slate-300 {{ $notif->read_at ? '' : 'ring-1 ring-blue-100 bg-blue-50/10' }}">
                    @if(!$notif->read_at)
                        <div class="absolute left-0 top-0 bottom-0 w-[3px] bg-blue-500"></div>
                    @endif
                    
                    <p class="text-[0.7rem] text-slate-500 pb-2 border-b border-slate-100 mb-3 truncate pr-2 font-medium uppercase tracking-tight">
                        {{ $notif->data['sender_name'] ?? 'System Administrator' }}
                    </p>
                    
                    <div class="flex flex-col">
                        <p class="text-sm text-slate-800 leading-relaxed font-bold">
                            {{ $notif->data['title'] ?? 'Notification' }}
                        </p>
                        <p class="text-sm text-slate-600 mt-1 leading-snug">{{ $notif->data['message'] ?? '' }}</p>
                        <p class="text-[0.65rem] text-slate-400 mt-2 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $notif->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center h-48 text-slate-400">
                    <svg class="w-12 h-12 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <p class="text-sm">No notifications yet</p>
                </div>
            @endforelse
         </div>
    </div>
</div>
