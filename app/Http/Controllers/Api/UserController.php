<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Contracts\Services\UserServiceInterface;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $user = $this->userService->create($data);

        return response()->json($user, 201);
    }

    public function show($id)
    {
        $user = $this->userService->findById($id);

        return response()->json($user);
    }

    public function update(UserRequest $request, $id)
    {
        $data = $request->validated();
        $user = $this->userService->update($id, $data);

        return response()->json($user);
    }

    public function destroy($id)
    {
        $this->userService->delete($id);

        return response()->json(null, 204);
    }

    public function findByUsername($username)
    {
        $user = $this->userService->findByUsername($username);

        return response()->json($user);
    }

    public function findByEmail($email)
    {
        $user = $this->userService->findByEmail($email);

        return response()->json($user);
    }
}
