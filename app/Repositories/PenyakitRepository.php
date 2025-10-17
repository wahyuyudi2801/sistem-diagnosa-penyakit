<?php

namespace App\Repositories;

use App\Models\Penyakit;

class PenyakitRepository {
    public function getAll(array $fields)
    {
        return Penyakit::select($fields)->latest()->lazy();
    }

    public function getById(array $fields, int $id)
    {
        return Penyakit::select($fields)->latest()->findOrFail($id);
    }

    public function getSelectRawAll(string $raw)
    {
        return Penyakit::selectRaw($raw)->orderBy('id', 'asc')->get();
    }

    public function create(array $data)
    {
        return Penyakit::create($data);
    }

    public function getCount()
    {
        return Penyakit::count();
    }
}
