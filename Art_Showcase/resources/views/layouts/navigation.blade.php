<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    {{-- Logo mengarah ke Dashboard --}}
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" style="max-height: 40px; width: auto;" />
                    </a>
                </div>

                {{-- NAVIGASI KIRI: LINK UTAMA --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    
                    {{-- Link UTAMA (Explore/Jelajahi Karya) --}}
                    <x-nav-link :href="route('homepage')" :active="request()->routeIs('homepage') || request()->routeIs('artworks.catalog')">
                        {{ __('Jelajahi Karya') }}
                    </x-nav-link>

                    @auth
                        {{-- 💡 PERBAIKAN: Tombol Dashboard Kondisional --}}
                        @if (Auth::user()->role === 'admin')
                            {{-- Jika ADMIN, arahkan ke route admin.dashboard --}}
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                {{ __('Manajemen Admin') }}
                            </x-nav-link>
                        @elseif (Auth::user()->role !== 'admin')
                            {{-- Jika MEMBER/CURATOR, arahkan ke route dashboard multi-role --}}
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- DROPDOWN KANAN: PROFILE DAN MANAJEMEN --}}
            @auth
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>    

                    <x-slot name="content">
                        
                        {{-- 💡 PERBAIKAN: Link Profile Settings dikembalikan untuk SEMUA ROLE --}}
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile Settings') }}
                        </x-dropdown-link>

                        {{-- 💡 PERBAIKAN: Link Manajemen Karya (Hanya untuk Role Member) --}}
                        @if(Auth::user()->role === 'member') 
                            <div class="border-t border-gray-100 my-1"></div>

                            <x-dropdown-link :href="route('member.artworks.index')">
                                {{ __('Kelola Karya') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('member.favorites.index')">
                                {{ __('My Favorites') }}
                            </x-dropdown-link>
                        
                        {{-- 💡 TAMBAHAN: Link Manajemen Challenge (Hanya untuk Role Curator) --}}
                        @elseif (Auth::user()->role === 'curator')
                            <div class="border-t border-gray-100 my-1"></div>

                            <x-dropdown-link :href="route('curator.challenges.index')">
                                {{ __('Kelola Challenge') }}
                            </x-dropdown-link>
                        @endif

                        {{-- Tambahkan pemisah jika ada link manajemen yang tampil --}}
                        @if (Auth::user()->role !== 'admin')
                            <div class="border-t border-gray-100 my-1"></div>
                        @endif
                        
                        {{-- Link Logout (Selalu ada) --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>

                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            {{-- 💡 Tampilkan Avatar di Dropdown --}}
                            <img src="{{ Auth::user()->avatar_path ? asset('storage/' . Auth::user()->avatar_path) : 'https://via.placeholder.com/20/CCCCCC/FFFFFF?text=A' }}" 
                                 alt="Avatar" class="rounded-full me-2" style="width: 24px; height: 24px; object-fit: cover;">
                            <div>{{ Auth::user()->name }}</div>
                            </button>
                    </x-slot>
                    
                </x-dropdown>
            </div>
            @endauth
            
            {{-- 💡 NAVIGASI GUEST KANAN: HANYA SATU BLOK INI YANG TERSISA --}}
            @guest
            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex sm:items-center">
                <x-nav-link :href="route('login')">{{ __('Log in') }}</x-nav-link>
                <x-nav-link :href="route('register')">{{ __('Register') }}</x-nav-link>
            </div>
            @endguest
            
            <div class="-me-2 flex items-center sm:hidden">
            </div>
        </div>
    </div>

    </nav>