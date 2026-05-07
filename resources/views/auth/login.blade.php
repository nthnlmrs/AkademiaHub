<x-guest-layout>
    <!-- Session Status -->
    @if(session('status'))
        <div class="alert alert-success mb-4">{{ session('status') }}</div>
    @endif

    <h2 class="text-[1.5rem] font-semibold text-[#1b1b1b] mb-4">Sign in</h2>


    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">

            <input id="email" class="w-full border-t-0 border-l-0 border-r-0 border-b border-gray-600 focus:border-b-2 focus:border-[#0067b8] focus:ring-0 px-0 py-1 bg-transparent text-[#1b1b1b]" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Email, phone, or Skype">
            @error('email')
                <p class="mt-1 text-sm text-[#e81123]">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">

            <input id="password" class="w-full border-t-0 border-l-0 border-r-0 border-b border-gray-600 focus:border-b-2 focus:border-[#0067b8] focus:ring-0 px-0 py-1 bg-transparent text-[#1b1b1b]" type="password" name="password" required autocomplete="current-password" placeholder="Password">
            @error('password')
                <p class="mt-1 text-sm text-[#e81123]">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center mb-6">
            <input id="remember_me" type="checkbox" class="w-4 h-4 text-[#0067b8] border-gray-500 rounded-none focus:ring-[#0067b8]" name="remember">
            <label for="remember_me" class="ml-2 text-sm text-slate-600 cursor-pointer">Remember me</label>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-[#0067b8] hover:bg-[#005da6] text-white py-1.5 px-8 transition-colors text-[15px] font-medium">
                Sign in
            </button>
        </div>
    </form>
</x-guest-layout>
