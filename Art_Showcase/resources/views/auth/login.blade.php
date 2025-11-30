<x-guest-layout>
    <div class="mb-6 text-center text-2xl font-bold text-gray-800">
        {{ __('Masuk ke Art Showcase') }}
    </div>

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="block font-medium text-sm text-gray-700">{{ __('Email') }}</label>
            <input id="email" class="block w-full border-gray-300 rounded-md shadow-sm mt-1 focus:border-indigo-500 focus:ring-indigo-500" 
                   type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
            @error('email')
                <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4 mb-4">
            <label for="password" class="block font-medium text-sm text-gray-700">{{ __('Password') }}</label>
            <input id="password" class="block w-full border-gray-300 rounded-md shadow-sm mt-1 focus:border-indigo-500 focus:ring-indigo-500"
                   type="password" name="password" required autocomplete="current-password" />
            @error('password')
                <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex justify-between items-center mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingat Saya') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="underline text-sm text-indigo-600 hover:text-indigo-900" href="{{ route('password.request') }}">
                    {{ __('Lupa Password?') }}
                </a>
            @endif
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('register') }}">
                {{ __('Belum punya akun?') }}
            </a>

            <button type="submit" class="ms-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Masuk') }}
            </button>
        </div>
    </form>
</x-guest-layout>