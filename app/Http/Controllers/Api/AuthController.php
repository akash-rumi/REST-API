<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\AuthResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $responseData = $this->authService->register($request->validated());
        return (new AuthResource($responseData))->response()->setStatusCode(201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $responseData = $this->authService->login($request->validated());
        return (new AuthResource($responseData))->response();
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}