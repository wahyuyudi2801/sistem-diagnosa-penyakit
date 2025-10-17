<?php

use App\Models\Rekap;

class RekapRepository
{
    public function getByUserId(int $userId, array $fields)
    {
        return Rekap::select($fields)->with([
            'rekap_gejalas.gejala',
            'rekap_penyakits' => function ($query) {
                $query->orderBy('persentase', 'desc')->with('penyakit');
            }
        ])->where('user_id', $userId)->latest()->get();
    }

    public function getAllJoinUserAndRole(array $fields)
    {
        return Rekap::with([
                'rekap_gejalas.gejala',
                'rekap_penyakits' => function ($query) {
                    $query->orderBy('persentase', 'desc')->with('penyakit');
                }
            ])->select($fields)
            ->join('users', 'users.id', '=', 'rekaps.user_id')
            ->join('roles', 'roles.id', '=', 'users.role_id')->latest()->get();
    }

    public function create(array $data)
    {
        return Rekap::create($data);
    }
}
