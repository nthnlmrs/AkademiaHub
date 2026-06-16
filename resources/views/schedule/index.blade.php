@section('title', 'Schedule')

<x-app-layout>
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold">Schedule</h2>
            <p class="text-slate-500 mt-1">View your class schedule by day</p>
        </div>

        <!-- Day Selector -->
        <div class="flex flex-wrap gap-2">
            @foreach($days as $i => $day)
                <a href="{{ route('schedule.index', ['day' => $i]) }}"
                   class="day-pill {{ $selectedDay == $i ? 'active' : '' }}">
                    {{ $day }}
                </a>
            @endforeach
        </div>

        <h3 class="text-lg font-semibold text-slate-600">
            {{ $days[$selectedDay] }}'s Schedule
            @if($selectedDay == now()->dayOfWeek)
                <span class="badge badge-success ml-2">Today</span>
            @endif
        </h3>

        <!-- Schedule List -->
        @if($schedules->count() > 0)
            <div class="space-y-3">
                @foreach($schedules as $schedule)
                    @php
                        $sessionTarget = $schedule->currentSession;
                        $href = $sessionTarget
                            ? route('class.session', [$schedule->classRoom, $sessionTarget])
                            : route('class.show', $schedule->classRoom);
                    @endphp
                    <a href="{{ $href }}" class="schedule-card block">
                        <div class="flex items-start gap-4">
                            <!-- Time Column -->
                            <div class="text-center min-w-[70px]">
                                <p class="text-xl font-bold text-indigo-400">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</p>
                                <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</p>
                            </div>

                            <!-- Divider -->
                            <div class="w-px h-16 bg-slate-200 self-center"></div>

                            <!-- Details -->
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-semibold text-slate-900">{{ $schedule->classRoom->course->name }}</h4>
                                    <span class="badge {{ $schedule->classRoom->type === 'LAB' ? 'badge-info' : 'badge-primary' }}">{{ $schedule->classRoom->type }}</span>
                                    <span class="badge {{ $schedule->classRoom->mode === 'online' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($schedule->classRoom->mode) }}</span>
                                </div>
                                <p class="text-sm text-slate-600">
                                    Class {{ $schedule->classRoom->name }} &middot;
                                    {{ $schedule->classRoom->course->code }}
                                    @if($sessionTarget)
                                        &middot; <span class="font-medium text-indigo-500">Session {{ $sessionTarget->session_number }}</span>
                                    @endif
                                </p>
                                <div class="flex items-center gap-4 mt-2 text-xs text-slate-500">
                                    @if($schedule->room || $schedule->classRoom->room)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            {{ $schedule->room ?: $schedule->classRoom->room }}
                                        </span>
                                    @endif
                                    @if($schedule->classRoom->lecturers->count() > 0)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            {{ $schedule->classRoom->lecturers->pluck('name')->join(', ') }}
                                        </span>
                                    @endif
                                    @if($schedule->classRoom->type === 'LAB' && $schedule->classRoom->teachingAssistants->count() > 0)
                                        <span class="flex items-center gap-1 text-cyan-400">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                            TA: {{ $schedule->classRoom->teachingAssistants->pluck('name')->join(', ') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="glass-card p-12 text-center text-slate-500">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p class="text-lg">No classes scheduled for {{ $days[$selectedDay] }}</p>
            </div>
        @endif
    </div>
</x-app-layout>
