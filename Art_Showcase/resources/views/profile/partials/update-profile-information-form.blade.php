<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    {{-- Form Verifikasi Email (harus terpisah) --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- ✅ FORM UTAMA PROFILE INFO: Method PATCH, Target profile.update --}}
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch') 
        
        {{-- INPUT AVATAR --}}
        <div class="mb-4 text-center">
            <img src="{{ $user->avatar_path ? asset('storage/' . $user->avatar_path) : 'https://via.placeholder.com/100/CCCCCC/FFFFFF?text=AVATAR' }}" 
                alt="Avatar" class="rounded-circle mb-2" style="width: 100px; height: 100px; object-fit: cover;">
            <x-input-label for="avatar" :value="__('Foto Profil')" />
            <input type="file" name="avatar" id="avatar" class="block w-full text-sm text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
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

        <div class="mb-4 text-center">
        {{-- PASTIKAN MENGGUNAKAN FALLBACK JIKA AVATAR_PATH KOSONG --}}
        <img src="{{ $user->avatar_path ? asset('storage/' . $user->avatar_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}" 
             alt="Avatar" class="rounded-circle mb-2" style="width: 100px; height: 100px; object-fit: cover;">
        
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>