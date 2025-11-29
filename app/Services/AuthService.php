<?php

namespace App\Services;
use App\Repositories\IAuthRepository;
use Auth;
class AuthService
{
    public function __construct(protected IAuthRepository $userRepository)
    {
    }
    public function login($email, $password)
    {
        $credentials = [
            'email' => $email,
            'password' => $password
        ];

        if (!Auth::attempt($credentials)) {
            return -1;
        }
        $user = Auth::user();
        $token = $this->userRepository->createAccessToken($user);
        return [
            'accessToken' => $token,
            'user' => $user
        ];
    }

    public function register($email, $password, $username)
    {
        $user = $this->userRepository->create(
            $email,
            $password,
            $username,
        );

        return $user;
    }
}