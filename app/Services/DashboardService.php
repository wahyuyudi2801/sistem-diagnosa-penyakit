<?php

use App\Repositories\GejalaRepository;
use App\Repositories\PenyakitRepository;
use App\Repositories\UserRepository;

class DashboardService
{
    private PenyakitRepository $penyakitRepository;
    private GejalaRepository $gejalaRepository;
    private UserRepository $userRepository;

    public function __construct(
        PenyakitRepository $penyakitRepository,
        GejalaRepository $gejalaRepository,
        UserRepository $userRepository,
    )
    {
        $this->penyakitRepository = $penyakitRepository;
        $this->gejalaRepository = $gejalaRepository;
        $this->userRepository = $userRepository;
    }

    public function getData()
    {
        $data = [];

        // data penyakit
        $data[] = (object)[
            'title' => 'Total Data Penyakit',
            'total' => $this->penyakitRepository->getCount(),
            'icon' => 'clipboard-list',
            'route' => 'penyakit.index'
        ];

        // data gejala
        $data[] = (object)[
            'title' => 'Total Data Gejala',
            'total' => $this->gejalaRepository->getCount(),
            'icon' => 'clipboard-list',
            'route' => 'gejala.index'
        ];

        // data users
        $data[] = (object)[
            'title' => 'Total Pengguna',
            'total' => $this->userRepository->getCount(),
            'icon' => 'user',
            'route' => 'users.index'
        ];

        return $data;
    }
}
