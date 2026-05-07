@section('title', 'Lab Admin Dashboard')

<x-app-layout>
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold">Welcome, Admin Laboratory</h2>
            <p class="text-slate-500 mt-1">Manage teaching assistants and lab classes</p>
        </div>

        <div class="glass-card p-6">
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <a href="{{ route('admin_lab.ta.index') }}" class="btn-primary justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7"/></svg>
                    Manage TAs
                </a>
                <a href="{{ route('admin_lab.classes.create') }}" class="btn-secondary justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Create Lab Class
                </a>
                <a href="{{ route('admin_lab.schedules.create') }}" class="btn-secondary justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Lab Schedule
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
