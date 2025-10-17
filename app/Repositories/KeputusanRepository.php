<?php

namespace App\Repositories;

use App\Models\Keputusan;

class KeputusanRepository
{
    public function getAll(array $fields)
    {
        return Keputusan::select($fields)->orderBy('id', 'asc')->get();
    }
    public function getSelectRawAll(string $raw)
    {
        return Keputusan::selectRaw($raw)->orderBy('id', 'asc')->get();
    }

    public function getSelectRawLeftJoinGejala(string $raw)
    {
        return Keputusan::selectRaw($raw)
            ->leftJoin('gejalas', 'gejalas.id', '=', 'keputusans.gejala_id')
            ->orderBy('id', 'asc')
            ->get();
    }

    public function getByPenyakitId(array $fields, $penyakitId)
    {
        return Keputusan::select($fields)->where('penyakit_id', $penyakitId)->get();
    }

    public function deleteByPenyakitId(int $penyakitId)
    {
        Keputusan::where('penyakit_id', $penyakitId)->delete();
    }

    public function create(array $data)
    {
        return Keputusan::create($data);
    }
}
