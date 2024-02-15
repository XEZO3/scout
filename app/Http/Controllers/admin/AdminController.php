<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admins;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    function store(Request $request){
        $formInputs = $request->validate([
            'name'=>'required|min:3',
            'username'=>'required|unique:users',
            'password'=>'required|min:4',
        ]);
        $formInputs['password']=bcrypt($formInputs['password']);
        
        $user = admins::create($formInputs);
        $name = $user['name'];
        $token = $user->createToken('MyAppToken')->plainTextToken;

        return response()->json([
            'message' => 'Welcome ' . $name,
            'access_token' => $token,
        ]);
    }
    function login(Request $request){
        $formInputs = $request->validate([
            'username'=>'required',
            'password'=>'required',
        ]);
        
        if(auth()->guard('admin')->attempt($formInputs)){
            $user = auth()->guard('admin')->user();
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
    function delete($id){

    }
    
}
