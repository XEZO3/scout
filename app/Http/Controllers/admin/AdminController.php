<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admins;
use Dotenv\Exception\ValidationException;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    function store(Request $request){
        $formInputs = $request->validate([
            'name'=>'required|min:3',
            'username'=>'required|unique:admins',
            'password'=>'required|min:4',
            'level'=>'nullable'
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

        $token = Auth::guard("admin")->attempt($formInputs);

        if (!$token) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::guard("admin")->user();
        return response()->json([
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);

        // $admin = admins::where('username', $formInputs['username'])->first();

        // if (!$admin || !Hash::check($formInputs['password'], $admin->password)) {
        //     throw ValidationException::withMessages([
        //         'username' => ['The provided credentials are incorrect.'],
        //     ]);
        // }
    
        // $token = $admin->createToken('MyAppToken')->plainTextToken;
        // return response()->json([
        //     'user' => $admin,
        //     'access_token' => $token,
        // ]);
    }
    public function logout(){
        auth()->guard('admin')->user()->tokens()->delete();
        return response()->json([
            'message' => "logout success",
            'result' => "",
        ]);
    }
    function delete($id){

    }
    
}
