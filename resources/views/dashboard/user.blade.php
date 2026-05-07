@section('title', 'Student Dashboard')

<x-app-layout>
    <div class="space-y-8">
        <!-- Top Section: Welcome & Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-2 flex flex-col justify-center">
                <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Welcome back, {{ Auth::user()->name }}! 👋</h2>
                <p class="text-slate-500 mt-2 text-lg">You have <span class="text-indigo-600 font-semibold">{{ $todaySchedules->count() }} classes</span> scheduled for today.</p>
            </div>
            
            <!-- Quick Stats -->
            <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div class="glass-card p-4 flex flex-col items-center justify-center text-center">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">GPA</span>
                    <span class="text-2xl font-black text-indigo-600">{{ number_format($gpa ?? 0, 2) }}</span>
                </div>
                <div class="glass-card p-4 flex flex-col items-center justify-center text-center">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Credits</span>
                    <span class="text-2xl font-black text-emerald-600">{{ $totalCredits }}</span>
                </div>
                <div class="glass-card p-4 flex flex-col items-center justify-center text-center hidden sm:flex">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Attendance</span>
                    <span class="text-2xl font-black text-amber-500">94%</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Left Column: Schedule & Announcements -->
            <div class="xl:col-span-2 space-y-8">
                
                <!-- Today's Classes -->
                <section>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            Today's Schedule
                        </h3>
                        <span class="text-xs font-medium text-slate-400 bg-slate-100 px-2 py-1 rounded-md">{{ now()->format('l, d M') }}</span>
                    </div>

                    @if($todaySchedules->count() > 0)
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($todaySchedules as $schedule)
                                <div class="glass-card group overflow-hidden border-l-4 {{ $schedule->classRoom->type === 'LAB' ? 'border-cyan-500' : 'border-indigo-500' }} hover:shadow-xl transition-all duration-300">
                                    <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div class="flex items-center gap-5">
                                            <div class="text-center min-w-[60px]">
                                                <p class="text-xl font-black text-slate-800">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</p>
                                                <p class="text-[0.7rem] font-bold text-slate-400 uppercase">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</p>
                                            </div>
                                            <div class="h-10 w-px bg-slate-100 hidden sm:block"></div>
                                            <div>
                                                <h4 class="font-bold text-slate-900 text-lg group-hover:text-indigo-600 transition-colors">{{ $schedule->classRoom->course->name }}</h4>
                                                <p class="text-sm text-slate-500 flex items-center gap-1.5 mt-0.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    {{ $schedule->room ?? $schedule->classRoom->room ?? 'Room TBA' }} &middot; Class {{ $schedule->classRoom->name }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between sm:justify-end gap-3 pt-4 sm:pt-0 border-t sm:border-t-0 border-slate-50">
                                            <div class="flex -space-x-2">
                                                @foreach($schedule->classRoom->lecturers->take(3) as $lecturer)
                                                    <div class="w-8 h-8 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center text-[0.6rem] font-bold text-slate-600" title="{{ $lecturer->name }}">
                                                        {{ substr($lecturer->name, 0, 1) }}
                                                    </div>
                                                @endforeach
                                            </div>
                                            <a href="{{ route('class.show', $schedule->classRoom) }}" class="px-4 py-2 bg-slate-900 text-white text-xs font-bold rounded-lg hover:bg-indigo-600 transition-colors shadow-sm">Enter Class</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="glass-card p-10 text-center text-slate-400">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="font-medium">Free day! No classes scheduled.</p>
                            <button class="mt-4 text-sm text-indigo-500 font-bold hover:underline">View Weekly Planner</button>
                        </div>
                    @endif
                </section>

                <!-- Announcements -->
                <section>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-rose-500/10 flex items-center justify-center text-rose-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            </span>
                            Latest Announcements
                        </h3>
                        <button @click="$store.notifications.show()" class="text-xs font-bold text-indigo-500 hover:text-indigo-700">View All</button>
                    </div>

                    <div class="space-y-4">
                        @forelse($recentNotifications as $notif)
                            <div class="glass-card p-4 hover:bg-white transition-colors cursor-pointer border-l-2 {{ $notif->read_at ? 'border-slate-100' : 'border-indigo-400 bg-indigo-50/5' }}">
                                <div class="flex gap-4">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-xs font-bold text-indigo-500 uppercase tracking-tighter">{{ $notif->data['sender_name'] ?? 'System' }}</p>
                                            <p class="text-[0.65rem] text-slate-400">{{ $notif->created_at->diffForHumans() }}</p>
                                        </div>
                                        <h5 class="font-bold text-slate-800 mt-0.5 truncate">{{ $notif->data['title'] }}</h5>
                                        <p class="text-sm text-slate-500 line-clamp-1 mt-0.5">{{ $notif->data['message'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-slate-400 text-sm italic">No recent announcements.</div>
                        @endforelse
                    </div>
                </section>
            </div>

            <!-- Right Column: Sidebar -->
            <div class="space-y-8">
                <!-- My Courses Grid -->
                <section>
                    <h3 class="text-xl font-bold text-slate-800 mb-4">My Enrolled Classes</h3>
                    <div class="space-y-3">
                        @forelse($classRooms->take(4) as $cr)
                            <a href="{{ route('class.show', $cr) }}" class="flex items-center gap-4 p-3 rounded-2xl hover:bg-white hover:shadow-md transition-all group border border-transparent hover:border-slate-100">
                                <div class="w-12 h-12 rounded-2xl {{ $cr->type === 'LAB' ? 'bg-cyan-500/10 text-cyan-600' : 'bg-indigo-500/10 text-indigo-600' }} flex flex-col items-center justify-center font-black">
                                    <span class="text-[0.6rem] uppercase opacity-60">{{ $cr->type }}</span>
                                    <span class="text-sm leading-none">{{ $cr->name }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 truncate transition-colors">{{ $cr->course->name }}</h5>
                                    <p class="text-[0.7rem] text-slate-400 font-medium uppercase tracking-wider">{{ $cr->course->code }}</p>
                                </div>
                                <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-400 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @empty
                            <div class="text-sm text-slate-400 italic">Not enrolled in any classes yet.</div>
                        @endforelse
                        @if($classRooms->count() > 4)
                            <button class="w-full py-2 text-xs font-bold text-slate-400 hover:text-indigo-500 transition-colors">Show {{ $classRooms->count() - 4 }} More...</button>
                        @endif
                    </div>
                </section>

                <!-- Upcoming Section -->
                <section class="glass-card p-6 bg-gradient-to-br from-indigo-600 to-violet-700 text-white border-0 shadow-indigo-200">
                    <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Upcoming This Week
                    </h3>
                    <div class="space-y-4">
                        @forelse($upcomingSchedules as $up)
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-white/40"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[0.65rem] font-bold text-white/60 uppercase">{{ \App\Models\Schedule::DAYS[$up->day_of_week] }} &middot; {{ \Carbon\Carbon::parse($up->start_time)->format('H:i') }}</p>
                                    <p class="text-sm font-bold truncate">{{ $up->classRoom->course->name }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-white/60 italic">No upcoming classes later this week.</p>
                        @endforelse
                    </div>
                    <hr class="my-5 border-white/10">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-white/80">Check full schedule</p>
                        <a href="{{ route('schedule.index') }}" class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center hover:bg-white text-white hover:text-indigo-600 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
