<?php
namespace App\Services\LeadersService;
use Illuminate\Support\Facades\Auth;
use App\Services\LeadersService\Interfaces\LeaderAuthServiceInterface;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class LeaderAuthService implements LeaderAuthServiceInterface
{
    public function login(array $credentials)
    {
        
          
            $token = Auth::guard("admin")->attempt($credentials);
    
            if (!$token) {
                return[
                    'success'=>false,
                    'message'=>"username or password is incorrect"
                ];
            }
    
            $user = Auth::guard("admin")->user();
            $refreshToken = JWTAuth::fromUser($user);
    
            return [
                'success'=>true,
                'refresh'=>$refreshToken,
                'access'=>$token,
                'user'=>$user
            ];
    }

    public function logout()
    {
        $user = Auth::guard('admin')->user();
        JWTAuth::invalidate(JWTAuth::getToken());

        // Invalidate the refresh token
        $refreshToken = request()->cookie('refresh_token');
        JWTAuth::setToken($refreshToken)->invalidate();

        return [
            'message' => "Logout success",
        ];
    }
    public function refresh($refreshToken)
    {
        try {
            $newToken = JWTAuth::refresh($refreshToken);
            return [
                'success'=>true,
                'access_token' => $newToken,
            ];
        } catch (\Exception $e) {
            return [
                'success'=>false,
                'message' => 'Invalid refresh token',
            ];
        }
    }
}
?>