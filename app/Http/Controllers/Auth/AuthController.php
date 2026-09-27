<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function index(){
        $user = Auth::user();
        return response()->json([
            'message' => 'Index',
        ]);
    }

    public function register(RegisterRequest $request){
        $user = User::create([
            'name' => $request->name,
            'role_id' => 2,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login_form')->with('success', 'Uspešno keriran nalog');
    }

    public function login(LoginRequest $request){
        $credentials = request(['email', 'password']);
        if(Auth::guard('web')->attempt($credentials)){
            return redirect('/');
        } else {
            return redirect()->back()->with('error', 'Pogrešni kredencijali');
        }
    }

    public function logout(Request $request){
        Auth::logout();
        return redirect('/');
    }

}
