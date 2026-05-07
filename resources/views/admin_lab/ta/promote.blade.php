@section('title', 'Promote to Teaching Assistant')

<x-app-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin_lab.ta.index') }}" class="text-sm text-slate-500 hover:text-indigo-400 transition-colors">← Back to TAs</a>
            <h2 class="text-2xl font-bold mt-2">Promote to Teaching Assistant</h2>
        </div>

        <!-- Student Info -->
        <div class="glass-card p-6 mb-6">
            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Student Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-slate-500">Name</p>
                    <p class="font-medium text-slate-900">{{ $user->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">NIM</p>
                    <p class="font-mono text-slate-900">{{ $user->nim_nip }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Email</p>
                    <p class="text-slate-600">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Current Status</p>
                    <span class="badge badge-success">Regular Student</span>
                </div>
            </div>
        </div>

        <!-- Promote Form -->
        <div class="glass-card p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-900">Switch to Teaching Assistant</h3>
                    <p class="text-sm text-slate-500">Assign a unique TA ID to promote this student</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin_lab.ta.execute_promotion', $user) }}" class="space-y-5">
                @csrf

                <div>
                    <label class="form-label">Teaching Assistant ID</label>
                    <input type="text" name="ta_id" value="{{ old('ta_id') }}" class="form-input" required placeholder="e.g. TA04823">
                    <p class="text-xs text-slate-500 mt-1">This ID must be unique. Example format: TA04823</p>
                    @error('ta_id')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        Promote to TA
                    </button>
                    <a href="{{ route('admin_lab.ta.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
