<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Contracts\Services\UserServiceInterface;
use Illuminate\Http\JsonResponse;

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
        $data = $request->validated();
        $user = $this->userService->create($data);

        return response()->json($user, 201);
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse
    {
        $user = $this->userService->findById($id);

        return response()->json($user);
    }

    /**
     * Update the specified user in storage.
     *
     * @param UserRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request, $id): JsonResponse
    {
        $data = $request->validated();
        $user = $this->userService->update($id, $data);

        return response()->json($user);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $this->userService->delete($id);

        return response()->json(null, 204);
    }

    /**
     * Find a user by username.
     *
     * @param string $username
     * @return \Illuminate\Http\JsonResponse
     */
    public function findByUsername($username): JsonResponse
    {
        $user = $this->userService->findByUsername($username);

        return response()->json($user);
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function findByEmail($email): JsonResponse
    {
        $user = $this->userService->findByEmail($email);

        return response()->json($user);
    }
}
