<img src="{{ asset('images/logo-jules.png') }}" 
     alt="JULES Platform Logo" 
     {{-- 💡 Perbaiki: Hapus style='width: auto; height: 100%;' dari merge --}}
     {{ $attributes->merge(['style' => 'max-width: 100%; height: 100%;']) }}
/>