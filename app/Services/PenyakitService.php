<?php

namespace App\Services;

use App\Repositories\PenyakitRepository;

class PenyakitService
{
    private PenyakitRepository $penyakitRepository;

    public function __construct(PenyakitRepository $penyakitRepository)
    {
        $this->penyakitRepository = $penyakitRepository;
    }

    public function getAll(array $fields = ['*'])
    {
        return $this->penyakitRepository->getAll($fields);
    }

    public function getById(int $id, array $fields = ['*'])
    {
        return $this->penyakitRepository->getById($fields, $id);
    }

    public function getSelectRawAll($raw)
    {
        return $this->penyakitRepository->getSelectRawAll($raw);
    }

    public function create(array $data)
    {
        return $this->penyakitRepository->create($data);
    }

}
