<?php
namespace App\Services;

use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class AuthService
 *
 * This class implements the AuthServiceInterface and handles the authentication-related operations.
 *
 * @package App\Services
 */
class AuthService implements AuthServiceInterface
{
    protected $userRepository;

    /**
     * AuthService constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Log in a user with the provided credentials.
     *
     * @param array $data
     * @return JsonResponse
     */
    public function login(array $data): JsonResponse
    {
        try {
            $user = $this->userRepository->findByEmail($data['email']);

            if (!$user || !Hash::check($data['password'], $user->password)) {
                return response()->json(
                    ['message' => __('auth.failed')],
                    401
                );
            }

            $token = $user->createToken('auth_token_' . $user->id)->plainTextToken;
            return response()->json(
                ['token' => $token],
                200
            );
        } catch (Exception $e) {
            Log::error('Login failed: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Login failed. Please try again later.'],
                500
            );
        }
    }

    /**
     * Log out the authenticated user.
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user instanceof User) {
                $this->userRepository->deleteTokens($user);
            }
            return response()->json(['message' => __('auth.logged_out')], 200);
        } catch (Exception $e) {
            Log::error('Logout failed: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Logout failed. Please try again later.'],
                500
            );
        }
    }
}
