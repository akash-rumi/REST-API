<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function register(array $data): array
    {
        $user = $this->userRepository->create($data);
        $this->userRepository->deleteTokens($user);
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $data): array
    {
        $identifier = $data['email'] ?? $data['phone'] ?? null;
        if (! $identifier) {
            throw ValidationException::withMessages(['identifier' => 'Email or phone is required.']);
        }

        $user = $this->userRepository->findByEmailOrPhone($identifier);

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => ['The provided credentials are incorrect.'],
            ]);
        }

        $this->userRepository->deleteTokens($user);
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $this->userRepository->deleteTokens($user);
    }
}