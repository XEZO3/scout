<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{   
    
    function login(Request $request){
        $formInputs = $request->validate([
            'username'=>'required',
            'password'=>'required',
        ]);
        
        if(auth()->attempt($formInputs)){
            $user = auth()->user();
            $token = $user->createToken('MyAppToken')->plainTextToken;
            return response()->json([
                'user' => $user,
                'access_token' => $token,
            ]);
        }else{
            return response()->json([
                'error' => 'username or password is incorrect',
            ], 401);
        }
    }
    function logout(Request $request){
        auth()->logout();
        return response()->json([
            'message' =>"done",
        ]);
    }
    
}
