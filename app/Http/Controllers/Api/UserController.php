<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Contracts\Services\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;

/**
 * Class UserController
 *
 * This class handles the user-related operations such as creating, retrieving, updating, and deleting users.
 *
 * @package App\Http\Controllers\Api
 */
class UserController extends Controller
{
    protected $userService;

    /**
     * UserController constructor.
     *
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Store a newly created user in storage.
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = $this->userService->create($data);
            return response()->json($user, 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(): JsonResponse
    {
        try {
            $userId = Auth::id();
            $user = $this->userService->findById($userId);
            if (!$user) {
                throw new NotFoundHttpException('User not found.');
            }
            return response()->json($user);
        } catch (NotFoundHttpException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the authenticated user in storage.
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $userId = Auth::id();
            $user = $this->userService->update($userId, $data);
            return response()->json($user);
        } catch (NotFoundHttpException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the authenticated user from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(): JsonResponse
    {
        try {
            $userId = Auth::id();
            $this->userService->delete($userId);
            return response()->json(null, 204);
        } catch (NotFoundHttpException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Find a user by username.
     *
     * @param string $username
     * @return \Illuminate\Http\JsonResponse
     */
    public function findByUsername($username): JsonResponse
    {
        try {
            $user = $this->userService->findByUsername($username);
            if (!$user) {
                throw new NotFoundHttpException('User not found.');
            }
            return response()->json($user);
        } catch (NotFoundHttpException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function findByEmail($email): JsonResponse
    {
        try {
            $user = $this->userService->findByEmail($email);
            if (!$user) {
                throw new NotFoundHttpException('User not found.');
            }
            return response()->json($user);
        } catch (NotFoundHttpException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
