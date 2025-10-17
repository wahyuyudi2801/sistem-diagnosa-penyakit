<?php

namespace App\Services;

use App\Repositories\GejalaRepository;

class GejalaService
{
    private GejalaRepository $gejalaRepository;

    public function __construct(GejalaRepository $gejalaRepository)
    {
        $this->gejalaRepository = $gejalaRepository;
    }

    public function getAll(array $fields = ['*'])
    {
        return $this->gejalaRepository->getAll($fields);
    }

    public function getById(array $fields = ['*'], int $id)
    {
        return $this->gejalaRepository->getAll($fields, $id);
    }

    public function getSelectRawAll(string $raw)
    {
        return $this->gejalaRepository->getSelectRawAll($raw);
    }

    public function  create(array $data)
    {
        return $this->gejalaRepository->create($data);
    }

    public function update(array $data, int $id)
    {
        return $this->gejalaRepository->update($data, $id);
    }

    public function delete(int $id): void
    {
        $this->gejalaRepository->delete($id);
    }
}
