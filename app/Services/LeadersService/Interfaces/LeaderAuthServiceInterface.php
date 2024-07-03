<?php
namespace App\Services\LeadersService\Interfaces;
interface LeaderAuthServiceInterface
{
    public function login(array $credentials);
    public function logout();
    public function refresh($refreshToken);
}
?>