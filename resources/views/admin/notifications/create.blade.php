@section('title', 'Blast Notification')

<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 border-b border-slate-200 pb-6">
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Blast Notification</h2>
            <p class="text-slate-500 mt-2 text-base">Broadcast important messages to specific user roles across the platform.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-8">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.notifications.store') }}" method="POST">
            @csrf

            <!-- Target Section -->
            <div class="mb-10">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">1. Select Target Audience</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="target" value="all" class="peer sr-only" checked>
                        <div class="flex items-center gap-4 p-5 rounded-2xl border border-slate-200 bg-white peer-checked:border-indigo-500 peer-checked:ring-1 peer-checked:ring-indigo-500 transition-all hover:shadow-md">
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center peer-checked:bg-indigo-100 peer-checked:text-indigo-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-800 peer-checked:text-indigo-700">All Users</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Broadcast to everyone</p>
                            </div>
                        </div>
                    </label>
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="target" value="lecturer" class="peer sr-only">
                        <div class="flex items-center gap-4 p-5 rounded-2xl border border-slate-200 bg-white peer-checked:border-indigo-500 peer-checked:ring-1 peer-checked:ring-indigo-500 transition-all hover:shadow-md">
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center peer-checked:bg-indigo-100 peer-checked:text-indigo-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-800 peer-checked:text-indigo-700">Lecturers</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Teaching staff only</p>
                            </div>
                        </div>
                    </label>
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="target" value="student" class="peer sr-only">
                        <div class="flex items-center gap-4 p-5 rounded-2xl border border-slate-200 bg-white peer-checked:border-indigo-500 peer-checked:ring-1 peer-checked:ring-indigo-500 transition-all hover:shadow-md">
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center peer-checked:bg-indigo-100 peer-checked:text-indigo-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-800 peer-checked:text-indigo-700">Students</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Enrolled students only</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Content Section -->
            <div class="mb-10">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">2. Compose Message</h3>
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Notification Title</label>
                        <input type="text" name="title" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-base py-3" required placeholder="e.g. System Maintenance, Important Announcement">
                        @error('title')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Message Body</label>
                        <textarea name="message" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-base py-3 h-40 resize-y" required placeholder="Write a clear and concise notification message..."></textarea>
                        @error('message')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4 pt-4 border-t border-slate-200">
                <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-xl shadow-sm transition-colors text-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Send Broadcast Now
                </button>
                <button type="reset" class="text-slate-500 hover:text-slate-700 font-medium py-3 px-4">
                    Clear Form
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
