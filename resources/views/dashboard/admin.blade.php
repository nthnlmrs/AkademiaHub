@section('title', 'Admin Dashboard')

<x-app-layout>
    <div class="space-y-6">
        <!-- Welcome -->
        <div>
            <h2 class="text-2xl font-bold">Welcome, Administrator</h2>
            <p class="text-slate-500 mt-1">Manage your learning platform</p>
        </div>

        <!-- Quick actions -->
        <div class="glass-card p-6">
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <a href="{{ route('admin.users.create') }}" class="btn-primary justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add User
                </a>
                <a href="{{ route('admin.courses.create') }}" class="btn-secondary justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Course
                </a>
                <a href="{{ route('admin.classrooms.create') }}" class="btn-secondary justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Class
                </a>
                <a href="{{ route('admin.schedules.create') }}" class="btn-secondary justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Schedule
                </a>
            </div>
        </div>
</x-app-layout>

