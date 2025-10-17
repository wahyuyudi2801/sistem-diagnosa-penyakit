<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService {
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAll(array $fields)
    {
        return $this->userRepository->getAll($fields);
    }

    public function getById(array $fields, int $id)
    {
        return $this->userRepository->getById($fields, $id);
    }

    public function create(array $data)
    {
        return $this->userRepository->create($data);
    }
}
