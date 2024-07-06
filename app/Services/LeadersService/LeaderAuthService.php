<?php
namespace App\Services\LeadersService;
use Illuminate\Support\Facades\Auth;
use App\Services\GeneralInterfaces\AuthServiceInterface;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Database\QueryException;
use App\Models\admins;
use App\Models\PersonalAccessToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class LeaderAuthService implements AuthServiceInterface
{
    public function login(array $credentials)
    {
            $login = Auth::guard("admin")->attempt($credentials);   
            if (!$login) {
                return $this->response(false,"username or password is incorrect",array());
            }    
            $user = Auth::guard("admin")->user();
            $refreshToken = $user->createToken('MyAppToken')->plainTextToken;
            $token= JWTAuth::fromUser($user);
            $result = [
                "access_token" =>$token,
                "refresh_token"=>$refreshToken
            ];   
            return $this->response(true,"",$result);
    }

    public function logout($refreshToken)
    {
        $user = Auth::guard('admin')->user();
        JWTAuth::invalidate(JWTAuth::getToken());

      
        $tokenParts = explode('|', $refreshToken);
        $tokenRecord = PersonalAccessToken::find($tokenParts[0]);
        $tokenRecord->delete();

        return [
            'message' => "Logout success",
        ];
    }
    public function refresh($refreshToken)
    {
            $tokenParts = explode('|', $refreshToken);
            $tokenId = $tokenParts[0];
            $plainTextToken = $tokenParts[1];
            $tokenRecord = PersonalAccessToken::find($tokenId);
            $tokenParts = explode('|', $refreshToken);
            if (count($tokenParts) !== 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid refresh token format'
                ], 401);
            }
            if (!$tokenRecord) {
                return $this->response(false,"wrong refresh token",array());
            }
            $hashedToken = hash('sha256', $plainTextToken);
            if ($hashedToken !=$tokenRecord->token) {
                return $this->response(false,"invalid token",array());
            }
            if ($tokenRecord->expires_at!=null&&Carbon::parse($tokenRecord->expires_at)->isPast()) {
                return $this->response(false,'Refresh token has expired',array());      
            }
            $user = admins::find($tokenRecord->tokenable_id);

            if (!$user) {
                return $this->response(false,"user not found",array());
            }
            $newToken = JWTAuth::fromUser($user);
            $result = [
                "access_token"=>$newToken
            ];
            return $this->response(true,"",$result);
       
    }
    public function create(array $credentials){

        try {
            // Attempt to create the user
            $user = admins::create($credentials);
            $name = $user['name'];
            
            try {
                $refreshToken = $user->createToken('MyAppToken')->plainTextToken;
                $accessToken = JWTAuth::fromUser($user);
            } catch (JWTException $e) {
                // Handle JWT token creation failure
                return $this->response(false,"cannot create token",array());
            }
    
            $result = [
                "access_token"=>$accessToken,
                "refresh_token"=>$refreshToken
            ];
            return $this->response(true,"",$result);
    
        } catch (QueryException $e) {
            // Handle database query exceptions, such as user creation failure
            return $this->response(false,"User creation failed",array());
            
        } catch (\Exception $e) {
            // Handle any other exceptions
            return $this->response(false,"some errors happen",array());
        }
    }
    protected function response(bool $success,string $message=null,array $result): array{
        return [
            "success"=>$success,
            "message"=>$message,
            "result"=>$result
        ];
    }

}
?>