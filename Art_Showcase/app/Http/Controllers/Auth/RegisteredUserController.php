<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Method ini dipanggil oleh Route::get('register', ...)
        return view('auth.register'); // Pastikan ini me-return view register yang benar
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', Rule::in(['member', 'curator'])], // Validasi untuk Role
        ]);

        $role = $request->role;
        // Curator = Pending (false), Member = Langsung Approved (true)
        $isApproved = ($role === 'curator') ? false : true; 

        $user = User::create([
            'name' => $request->name,
            'display_name' => $request->name, 
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role, 
            'is_approved' => $isApproved, 
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}