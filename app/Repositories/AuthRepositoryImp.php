<?php

namespace App\Repositories;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Hash;
class AuthRepositoryImp implements IAuthRepository
{

    public function __construct(
        protected User $user
    ) {
    }
    public function findByEmail($email)
    {
        return $this->user->where("email", $email)->first();
    }

    public function create($email, $password, $username)
    {
        $existingUser = $this->findByEmail($email);
        if ($existingUser) {
            throw new \Exception('Email already registered', 409);
        }

        return DB::transaction(function () use ($email, $password, $username) {
            $user = $this->user->create([
                "email" => $email,
                "password" => Hash::make($password),
                "name" => $username
            ]);
            $accessToken = $this->createAccessToken($user);
            return [
                'user' => $user,
                'accessToken' => $accessToken
            ];
        });
    }

    public function createAccessToken($user)
    {
        return $user->createToken("auth_token")->plainTextToken;
    }

    public function deleteAccessToken($user)
    {
        return $user->currentAccessToken()->delete();
    }
}