@section('title', 'Profile')

<x-app-layout>
    <div class="max-w-2xl space-y-6">
        <div>
            <h2 class="text-2xl font-bold">Profile Settings</h2>
            <p class="text-slate-500 mt-1">Manage your account settings</p>
        </div>

        <!-- Interactive Text Reader Settings -->
        <div class="glass-card p-6" x-data="ttsSettings()">
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Interactive Reader Settings</h3>
            <p class="text-sm text-slate-500 mb-4">Pengaturan untuk fitur pembaca teks interaktif pada halaman materi.</p>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                @csrf
                @method('patch')

                <!-- Toggle TTS -->
                <div class="flex items-center justify-between">
                    <div>
                        <label class="font-medium text-slate-900">Aktifkan Suara (Text-to-Speech)</label>
                        <p class="text-xs text-slate-500">Membaca teks secara otomatis saat diklik atau digeser.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="tts_enabled" value="1" class="sr-only peer" {{ Auth::user()->tts_enabled ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <!-- Voice Selection -->
                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-1">Pilih Suara / Bahasa</label>
                    <select name="tts_voice" class="w-full bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5">
                        <option value="">Default (Deteksi Otomatis)</option>
                        <template x-for="voice in voices" :key="voice.name">
                            <option :value="voice.name" x-text="`${voice.name} (${voice.lang})`" :selected="voice.name === '{{ Auth::user()->tts_voice }}'"></option>
                        </template>
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Sistem akan memprioritaskan suara bahasa Indonesia (id-ID) jika "Default" dipilih.</p>
                </div>

                <!-- Hidden Required Fields for ProfileUpdateRequest -->
                <input type="hidden" name="name" value="{{ Auth::user()->name }}">
                <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                <input type="hidden" name="is_tts_form" value="1">

                <button type="submit" class="btn-primary w-full text-center mt-2">Simpan Pengaturan Reader</button>
            </form>
        </div>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('ttsSettings', () => ({
                    voices: [],
                    init() {
                        const populateVoices = () => {
                            this.voices = window.speechSynthesis.getVoices();
                        };
                        populateVoices();
                        if (speechSynthesis.onvoiceschanged !== undefined) {
                            speechSynthesis.onvoiceschanged = populateVoices;
                        }
                    }
                }))
            })
        </script>

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
