<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $user = User::find($id);
        if ($user) {
            return $user->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $user = User::find($id);
        if ($user) {
            return $user->delete();
        }
        return false;
    }

    public function findByUsername(string $username): ?User
    {
        return User::where('name', $username)->first();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function deleteTokens(User $user): void
    {
        $user->tokens()->delete();
    }
}
