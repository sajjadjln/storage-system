<?php
namespace App\Repositories; 
interface IAuthRepository
{
    public function findByEmail($email);
    public function create($email, $password, $username);
    public function createAccessToken(object $user);
    public function deleteAccessToken(object $user);
}