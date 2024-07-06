<?php

namespace App\Http\Controllers\admin;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

use App\Http\Controllers\Controller;
use App\Models\admins;
use Illuminate\Http\Request;
use App\Services\GeneralInterfaces\AuthServiceInterface;
class AdminAuthController extends Controller
{
    private $leaderAuthService;
    public function __construct(AuthServiceInterface $leaderService) {
        $this->leaderAuthService = $leaderService;
    }
    function store(Request $request){
        $formInputs = $request->validate([
            'name'=>'required|min:3',
            'username'=>'required|unique:admins',
            'password'=>'required|min:4',
            'level'=>'required'
        ]);
        $formInputs['password']=bcrypt($formInputs['password']);
        $response = $this->leaderAuthService->create($formInputs);
        $refreshToken  = $response['result']['refresh_token'];
        unset($response['result']['refresh_token']);
        return response()->json($response)->cookie('refresh_token', $refreshToken, 60 * 24 * 7, null, null, true, true, false, 'Strict');;
        
    }
    function login(Request $request){
        $formInputs = $request->validate([
            'username'=>'required',
            'password'=>'required',
        ]);
        $response=$this->leaderAuthService->login($formInputs);
       if($response['success']){
        $refreshToken  = $response['result']['refresh_token'];
        unset($response['result']['refresh_token']);
        return response()->json($response)->cookie('refresh_token',  $refreshToken, 60 * 24 * 7, null, null, true, true, false, 'Strict');
       }
       return response()->json($response);
    }
    public function refresh(Request $request)
    {
        $refreshToken = $request->cookie('refresh_token');
        $response = $this->leaderAuthService->refresh($refreshToken);
        return response()->json($response);
    }

    public function logout(Request $request){
        $refreshToken = $request->cookie('refresh_token');
        $response = $this->leaderAuthService->logout( $refreshToken);
        return response()->json($response)->cookie('refresh_token', '', -1, '/', null, true, true, false, 'Strict');
    }
   
    
}
