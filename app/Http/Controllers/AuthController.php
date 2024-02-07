<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $name = "resaka";
        $email = $request->get('email');
        $password = $request->get('password');

        $create = [
            'name' => $name,
            'email' => $email,
            'password' => $password
        ];

        $q = User::create($create);

        if ($q) {
            return 'ok';
        } else {
            return 'fail';
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken('resaka')->plainTextToken;
    }

    public function test(){
        return 'API SUCCESS';
    }
}
