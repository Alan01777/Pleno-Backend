<?php

namespace App\Contracts\Services;
use \Illuminate\Http\JsonResponse;

interface AuthServiceInterface
{
    public function login(array $data): JsonResponse;
    public function logout(): void;
}
