<?php
namespace App\Services\GeneralInterfaces;
interface AuthServiceInterface
{
    public function login(array $credentials);
    public function logout($refreshToken);
    public function refresh($refreshToken);
    public function create(array $credentials);
}

?>