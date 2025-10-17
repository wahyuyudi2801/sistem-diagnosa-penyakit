<?php

class RekapService
{
    private RekapRepository $rekapRepository;

    public function __construct(RekapRepository $rekapRepository)
    {
        $this->rekapRepository = $rekapRepository;
    }

    public function getByUserId(int $userId, array $fields = ['*'])
    {
        return $this->rekapRepository->getByUserId($userId, $fields);
    }

    public function getAllJoinUserAndRole(array $fields = ['*'])
    {
        return $this->rekapRepository->getAllJoinUserAndRole($fields);
    }

    public function create(array $data, int $userId)
    {
        $rekap_gejalas = [];
        $rekap_penyakits = [];

        foreach ($data['rekap_gejalas'] as $value) {
            $rekap_gejalas[] = [
                'gejala_id' => $value,
            ];
        }

        foreach ($data['rekap_penyakits'] as $value) {
            $rekap_penyakits[] = [
                'penyakit_id' => $value['penyakit_id'],
                'persentase' => $value['persentase'],
            ];
        }

        $rekap = $this->rekapRepository->create(['user_id' => $userId]);
        $rekap->rekap_gejalas()->createMany($rekap_gejalas);
        $rekap->rekap_penyakits()->createMany($rekap_penyakits);

        return $rekap;
    }
}
