<?php
namespace App\Services;

use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AuthService implements AuthServiceInterface
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(array $data): JsonResponse
    {
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
    }

    public function logout(): void
    {
        $user = Auth::user();
        if ($user instanceof User) {
            $this->userRepository->deleteTokens($user);
        }
    }
}
