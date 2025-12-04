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

    public function create(): View
    {
        return view('auth.register'); 
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', Rule::in(['member', 'curator'])],
        ]);

        $role = $request->role;

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

        if ($role === 'member') {
            return redirect()->route('login')->with('success', 'Pendaftaran Member berhasil! Silakan masuk.');
        } else {
            return redirect()->route('login')->with('success', 'Pendaftaran Curator berhasil! Akun Anda menunggu persetujuan Admin.');
        }
    }
}