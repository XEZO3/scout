<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admins;
use Illuminate\Http\Request;
use App\Services\LeadersService\Interfaces\LeaderAuthServiceInterface;
class AdminAuthController extends Controller
{
    private $leaderAuthService;
    public function __construct(LeaderAuthServiceInterface $leaderService) {
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

        $response=$this->leaderAuthService->login($formInputs);
        if($response['success']){
            return response()->json(
                [
                'success' => true,
                'user'=>$response['user'],
                'access_token'=>$response['access']
                ]
            )->cookie('refresh_token', $response['refresh'], 60 * 24 * 7, null, null, true, true, false, 'Strict');
            }else{
                return response()->json([
                    'success'=>false,
                    'message'=>$response['message']
                ],401);
            }
    }
    public function refresh(Request $request)
    {
        $refreshToken = $request->cookie('refresh_token');
        $response = $this->leaderAuthService->refresh($refreshToken);
        return response()->json($response);
    }

    public function logout(){
        $response = $this->leaderAuthService->logout();
        return response()->json($response)->cookie('refresh_token', '', -1, '/', null, true, true, false, 'Strict');
    }
   
    
}
