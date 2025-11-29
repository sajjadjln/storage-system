<?php
namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AuthResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, AuthService $authService)
    {
        try {
            $user = $authService->register(
                $request->email,
                $request->password,
                $request->username
            );

            return (new AuthResource($user))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logIn(LoginRequest $request, AuthService $authService)
    {
        try {
            $authServResp = $authService->login($request->email, $request->password);
            if ($authServResp == -1) {
                return response()->json(
                    ["message" => "the provided credintials are wrongs"],
                    Response::HTTP_UNAUTHORIZED
                );
            }
            return new AuthResource($authServResp);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'login failed',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logOut(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}

