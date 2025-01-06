<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\Services\AuthServiceInterface;
use App\Http\Requests\AuthRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Class AuthController
 *
 * This controller handles the authentication actions such as login and logout.
 *
 * @package App\Http\Controllers\Api
 */

class AuthController extends Controller
{
    protected $authService;

    /**
     * AuthController constructor.
     *
     * @param AuthServiceInterface $authService The authentication service interface.
     */
    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle the login request.
     *
     * @param AuthRequest $request The authentication request containing user credentials.
     * @return JsonResponse The response containing the authentication token or error message.
     */

    public function login(AuthRequest $request): JsonResponse
    {
        try{
            $data = $request->validated();
            return $this->authService->login($data);
        } catch (Exception $e) {
            Log::error('Login failed: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Login failed. Please try again later.'],
                500
            );
        }
    }

    /**
     * Handle the logout request.
     *
     * @return JsonResponse The response confirming the user has been logged out.
     */
    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();
            return response()->json(['message' => 'Logged out successfully']);
        } catch (Exception $e) {
            Log::error('Logout failed: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Logout failed. Please try again later.'],
                500
            );
        }
    }
}
