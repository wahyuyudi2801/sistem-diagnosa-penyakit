<?php

namespace App\Repositories;

use App\Models\Gejala;

class GejalaRepository
{
    public function getAll(array $fields)
    {
        return Gejala::select($fields)->latest()->lazy();
    }

    public function getById(array $fields, int $id)
    {
        return Gejala::select($fields)->latest()->findOrFail($id);
    }

    public function create(array $data)
    {
        return Gejala::create($data);
    }

    public function getSelectRawAll(string $raw)
    {
        return Gejala::selectRaw($raw)->orderBy('id', 'asc')->get();;
    }

    public function update(array $data, int $id)
    {
        $gejala = Gejala::findOrFail($id);

        return $gejala->update($data);
    }

    public function delete(int $id)
    {
        $gejala = Gejala::findOrFail($id);

        return $gejala->delete($id);
    }

    public function getCount()
    {
        return Gejala::count();
    }
}
