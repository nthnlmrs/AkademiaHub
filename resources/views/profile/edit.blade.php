@section('title', 'Profile')

<x-app-layout>
    <div class="max-w-2xl space-y-6">
        <div>
            <h2 class="text-2xl font-bold">Profile Settings</h2>
            <p class="text-slate-500 mt-1">Manage your account settings</p>
        </div>

        <!-- Profile Info -->
        <div class="glass-card p-6">
            <h3 class="text-lg font-semibold mb-4">Account Information</h3>
            <div class="space-y-3">
                <div>
                    <label class="form-label">Name</label>
                    <p class="text-slate-900">{{ Auth::user()->name }}</p>
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <p class="text-slate-900">{{ Auth::user()->email }}</p>
                </div>
                <div>
                    <label class="form-label">Role</label>
                    <p class="text-slate-900">{{ Auth::user()->role_label }}</p>
                </div>
                @if(Auth::user()->nim_nip)
                <div>
                    <label class="form-label">NIM / NIP</label>
                    <p class="text-slate-900 font-mono">{{ Auth::user()->nim_nip }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Update Password -->
        <div class="glass-card p-6">
            <h3 class="text-lg font-semibold mb-4">Update Password</h3>
            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-input" required>
                    @error('current_password', 'updatePassword')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-input" required>
                    @error('password', 'updatePassword')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-input" required>
                </div>
                <button type="submit" class="btn-primary">Update Password</button>
            </form>
        </div>
    </div>
</x-app-layout>
