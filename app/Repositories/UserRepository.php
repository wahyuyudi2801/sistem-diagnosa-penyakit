<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository {
    public function getAll(array $fields)
    {
        return User::select($fields)->with(['role'])->lazy();
    }

    public function getById(array $fields, int $id)
    {
        return User::select($fields)->with(['role'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id']
        ]);
    }

    public function getCount()
    {
        return User::count();
    }
}
