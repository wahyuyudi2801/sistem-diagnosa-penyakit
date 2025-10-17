<?php

namespace App\Services;

use App\Repositories\KeputusanRepository;

class KeputusanService
{
    private KeputusanRepository $keputusanRepository;
    private GejalaService $gejalaService;

    public function __construct(
        KeputusanRepository $keputusanRepository,
        GejalaService $gejalaService
    ) {
        $this->keputusanRepository = $keputusanRepository;
        $this->gejalaService = $gejalaService;
    }

    public function getAll(array $fields = ['*'])
    {
        return $this->keputusanRepository->getAll($fields);
    }

    public function getDatatable(object $gejalas, object $penyakits, object $keputusans)
    {
        $datatable = collect([]);

        foreach ($gejalas as $gejala) {
            $kode_gejala = $gejala->kode;

            $array_keputusan = collect([]);
            foreach ($penyakits as $penyakit) {
                $kode_penyakit = $penyakit->kode;
                $result_filter = $keputusans->filter(fn($item) => $item->kode_gejala == $kode_gejala && $item->kode_penyakit == $kode_penyakit)->count();
                $array_keputusan->push($result_filter ? $result_filter : '-');
            }

            $datatable->push([
                'kode_gejala' => $kode_gejala,
                'keputusans' => $array_keputusan,
            ]);
        }

        return $datatable;
    }

    public function getSelectRawAll($raw)
    {
        return $this->keputusanRepository->getSelectRawAll($raw);
    }

    public function getSelectRawLeftJoinGejala(string $raw)
    {
        return $this->keputusanRepository->getSelectRawLeftJoinGejala($raw);
    }

    public function getByPenyakitId($penyakitId, array $fields = ['*'])
    {
        return $this->keputusanRepository->getByPenyakitId($fields, $penyakitId);
    }

    public function deleteByPenyakitId(int $penyakitId)
    {
        $this->keputusanRepository->deleteByPenyakitId($penyakitId);
    }

    public function create(array $data)
    {
        return $this->keputusanRepository->create($data);
    }

    public function creates(array $data, int $penyakitId): void
    {
        foreach ($data as $item) {
            $gejala_id = $item['gejala_id'];

            // buat gejala jika tidak ada
            if ($gejala_id === null) {
                $gejala = $this->gejalaService->create([
                    'nama_gejala' => $item['nama_gejala'],
                ]);
                $gejala_id = $gejala->id;
            }

            $this->create([
                'gejala_id' => $gejala_id,
                'penyakit_id' => $penyakitId,
            ]);
        }
    }
}
