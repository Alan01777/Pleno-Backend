<?php

namespace App\Contracts\Services;

use Illuminate\Http\JsonResponse;

/**
 * Interface AuthServiceInterface
 *
 * This interface defines the methods for authentication services.
 *
 * @package App\Contracts\Services
 */
interface AuthServiceInterface
{
    /**
     * Log in a user with the provided credentials.
     *
     * @param array $data The user credentials.
     * @return JsonResponse The JSON response containing the authentication result.
     */
    public function login(array $data): JsonResponse;

    /**
     * Log out the authenticated user.
     *
     * @return void The JSON response containing the authentication result.
     */
    public function logout(): void;
}
