@section('title', 'Edit TA ID')

<x-app-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin_lab.ta.index') }}" class="text-sm text-slate-500 hover:text-indigo-400 transition-colors">← Back to TAs</a>
            <h2 class="text-2xl font-bold mt-2">Edit TA ID: {{ $user->name }}</h2>
        </div>

        <div class="glass-card p-6">
            <div class="flex items-center gap-4 mb-5 pb-5 border-b border-slate-200/50">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-slate-900 font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                    <p class="text-sm text-slate-500">{{ $user->email }} &middot; NIM: {{ $user->nim_nip }}</p>
                </div>
                <span class="badge badge-info ml-auto">Teaching Assistant</span>
            </div>

            <form method="POST" action="{{ route('admin_lab.ta.update_id', $user) }}" class="space-y-5">
                @csrf @method('PUT')

                <div>
                    <label class="form-label">Teaching Assistant ID</label>
                    <input type="text" name="ta_id" value="{{ old('ta_id', $user->ta_id) }}" class="form-input" required placeholder="e.g. TA04823">
                    @error('ta_id')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary">Update TA ID</button>
                    <a href="{{ route('admin_lab.ta.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
