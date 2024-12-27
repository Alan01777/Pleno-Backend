<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\Services\AuthServiceInterface;
use App\Http\Requests\AuthRequest;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function login(AuthRequest $request): JsonResponse
    {
        return $this->authService->login($request->validated());
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();
        return response()->json(['message' => __('auth.logged_out')], 200);
    }
}
