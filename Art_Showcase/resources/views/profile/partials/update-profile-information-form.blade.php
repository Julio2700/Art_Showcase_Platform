<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    {{-- Form Verifikasi Email (dibiarkan terpisah) --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- ✅ FORM UTAMA PROFILE INFO: Method PATCH, Target profile.update --}}
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch') 
        
        {{-- 💡 INPUT AVATAR (Dikembalikan & Ditempatkan di sini) --}}
       <div class="mb-4 text-center">
    {{-- ✅ Pastikan Path Fallback ini berfungsi dan menggunakan path DB --}}
    <img src="{{ $user->avatar_path ? asset('storage/' . $user->avatar_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=007BFF&color=fff&size=100' }}" 
        alt="Avatar" class="rounded-circle mb-2" style="width: 100px; height: 100px; object-fit: cover;">
    
    </div>
        
        {{-- INPUT DISPLAY NAME --}}
        <div>
            <x-input-label for="display_name" :value="__('Display Name Publik')" />
            <x-text-input id="display_name" name="display_name" type="text" class="mt-1 block w-full" :value="old('display_name', $user->display_name)" autocomplete="display_name" />
            <x-input-error class="mt-2" :messages="$errors->get('display_name')" />
        </div>

        {{-- INPUT NAME --}}
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>
        
        {{-- INPUT EMAIL --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            @endif
        </div>
        
        {{-- ❌ INPUT BIO DIHAPUS DARI SINI --}}
        
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            </div>
    </form>
</section>