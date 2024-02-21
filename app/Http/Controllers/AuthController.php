<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;



class AuthController extends Controller
{

public function registerAdmin(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users|regex:/@apex\.com$/',
        'password' => 'required|string|min:12',
    ]);

    if ($validator->fails()) {

        $this->createAuditLog($request->name, $request->email, false);
        return response()->json(['errors' => $validator->errors()], 422);
    }



    $minPasswordLength = 12;

    if (strlen($request->password) < $minPasswordLength) {
        return response()->json(['errors' => 'Password must be at least ' . $minPasswordLength . ' characters'], 422);
    }

    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = Hash::make($request->password);
    $user->role = 'admin';
    $user->save();

    $this->createAuditLog($request->name, $request->email, true);

    $token = $user->createToken('AdminToken')->plainTextToken;

    return response()->json(['token' => $token], 201);
}


private function createAuditLog($name, $email, $success)
{
    $auditLog = new AuditLog();
    $auditLog->name = $name;
    $auditLog->email = $email;
    $auditLog->registration_time = now();
    $auditLog->success = $success;
    $auditLog->save();
}

public function registerUser(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8', 
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }


    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = Hash::make($request->password);
    $user->role = 'user'; 
    $user->save();

    $token = $user->createToken('UserToken')->plainTextToken;

    return response()->json(['token' => $token], 201);
}

public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $role = $user->role;
        $token = $user->createToken('AuthToken')->plainTextToken; 

        return response()->json(['token' => $token, 'role' => $role], 200);
    }

    return response()->json(['error' => 'Unauthorized'], 401);
}

public function logout(Request $request) {
    if ($request->user()) { 
        $request->user()->tokens()->delete();
    }

    return response()->json(['message' => 'Logged out successfully']);
}


}
