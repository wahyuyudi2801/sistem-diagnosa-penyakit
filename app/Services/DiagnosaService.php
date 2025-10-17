<?php

namespace App\Services;


class DiagnosaService
{
    private PenyakitService $penyakitService;
    private GejalaService $gejalaService;
    private KeputusanService $keputusanService;

    public function __construct(
        PenyakitService $penyakitService,
        GejalaService $gejalaService,
        KeputusanService $keputusanService
    ) {
        $this->penyakitService = $penyakitService;
        $this->gejalaService = $gejalaService;
        $this->keputusanService = $keputusanService;
    }

    public function naiveBayes($data)
    {
        // data penyakit
        $data_penyakit = $this->penyakitService->getSelectRawAll("id, CONCAT('P', id) as kode, nama_penyakit, solusi");

        // data keputusan
        $data_keputusan = $this->keputusanService->getAll();

        // data gejala
        $gejalas = $this->gejalaService->getAll();
        $data_gejala = $this->getGejala($gejalas, $data_penyakit, $data_keputusan);

        // probabilitas awal
        $probabilitas_awal = $this->probabilitasAwal($data_penyakit);

        // data pengujian
        $data_pengujian = $this->getDataPengujian($data);

        // + probabilitas kondisional
        $data_pengujian = $this->probabilitasKondisional($data_pengujian, $data_gejala);

        // + probabilitas posterior
        $data_pengujian = $this->probabilitasPosterior($data_pengujian, $probabilitas_awal);

        // total probabilitas untuk setiap penyakit
        $total_probabilitas = $this->totalProbabilitas($data_pengujian);

        // normalisasi probabilitas, chart data, dan solusi
        $chart_data = $this->chartData($total_probabilitas, $data_penyakit);
        $normalisasi_probabilitas = $this->normalisasiProbabilitas($total_probabilitas);

        return [
            'probabilitas_awal' => $probabilitas_awal,
            'data_pengujian' => $data_pengujian,
            'total_probabilitas' => $total_probabilitas,
            'normalisasi_probabilitas' => $normalisasi_probabilitas,
            'penyakits' => $data_penyakit,
            'chart_data' => $chart_data
        ];
    }

    private function getGejala($gejalas, $data_penyakit, $data_keputusan)
    {
        $data_gejala = collect([]);
        $gejalas->each(function ($item) use ($data_penyakit, $data_keputusan, $data_gejala) {
            $gejala_id = $item->id;
            $keputusan = collect([]);

            $data_penyakit->each(function ($item2) use ($data_keputusan, $gejala_id, $keputusan) {
                $penyakit_id = $item2->id;
                $filtered = $data_keputusan->filter(fn($item) => $item->penyakit_id == $penyakit_id && $item->gejala_id == $gejala_id);
                $keputusan->push($filtered->count() > 0 ? $filtered->count() : '-');
            });

            $data_gejala->push((object)[
                'gejala_id' => $gejala_id,
                'keputusan' => $keputusan
            ]);
        });

        return $data_gejala;
    }

    private function probabilitasAwal($data_penyakit)
    {
        $probabilitas_awal = collect([]);
        $data_penyakit->each(function ($item) use ($data_penyakit, $probabilitas_awal) {
            $penyakit_id = $item->id;
            $filtered = $data_penyakit->filter(fn($item2) => $item2->id == $penyakit_id);
            $probabilitas_awal->push(round($filtered->count() / $data_penyakit->count(), 2));
        });

        return $probabilitas_awal;
    }

    private function getDataPengujian($data)
    {
        $data_pengujian = collect([]);
        foreach ($data as $value) {
            $data_pengujian->push((object)[
                'gejala_id' => $value['gejala_id'],
                'nama_gejala' => $value['nama_gejala'],
                'prob_kondisional' => [],
                'prob_posterior' => []
            ]);
        }

        return $data_pengujian;
    }

    private function probabilitasKondisional($data_pengujian, $data_gejala)
    {
        // * probabilitas kondisional
        return $data_pengujian->each(function ($item) use ($data_gejala, $data_pengujian) {
            $gejala_id = $item->gejala_id;
            $filtered = $data_gejala->filter(fn($gejala) => $gejala->gejala_id == $gejala_id);
            $gejala_with_keputusan = $filtered->first();
            $keputusan = $gejala_with_keputusan->keputusan;
            foreach ($keputusan as $key => $value) {
                if ($value != '-') {
                    $keputusan[$key] = round($value / $data_pengujian->count(), 2);
                }
            }
            $data_pengujian->where('gejala_id', $gejala_id)->first()->prob_kondisional = $keputusan;
        });
    }

    private function probabilitasPosterior($data_pengujian, $probabilitas_awal)
    {
        // * probabilitas posterior
        return $data_pengujian->each(function ($item) use ($data_pengujian, $probabilitas_awal) {
            $prob_kondisional = $item->prob_kondisional;
            $filtered_prob_kondisional = $prob_kondisional->filter(fn($value) => $value != '-');
            $jml_prob_kondisional = $filtered_prob_kondisional->count();

            foreach ($prob_kondisional as $key => $nilai_prob_kondisional) {
                $nilai_prob_awal = $probabilitas_awal[$key];

                if ($nilai_prob_kondisional != '-') {
                    $result = ($nilai_prob_awal * $nilai_prob_kondisional) / (($nilai_prob_awal * $nilai_prob_kondisional) * $jml_prob_kondisional);
                    $data_pengujian->where('gejala_id', $item->gejala_id)->first()->prob_posterior[$key] = round($result, 2);
                } else {
                    $data_pengujian->where('gejala_id', $item->gejala_id)->first()->prob_posterior[$key] = $nilai_prob_kondisional;
                }
            }
        });
    }

    private function totalProbabilitas($data_pengujian)
    {
        $total_probabilitas = collect([]);
        $data_pengujian->each(function ($item) use ($total_probabilitas) {
            $prob_posterior = $item->prob_posterior;
            foreach ($prob_posterior as $key => $value) {
                $nilai_prob_posterior = $value;
                if ($nilai_prob_posterior == '-') {
                    $nilai_prob_posterior = 0;
                }
                if (!isset($total_probabilitas[$key])) {
                    $total_probabilitas[$key] = 0;
                }
                $total_probabilitas[$key] += round($nilai_prob_posterior, 2);
            }
        });

        return $total_probabilitas;
    }

    private function chartData($total_probabilitas, $data_penyakit)
    {
        $chart_data = collect([]);
        $sum = round($total_probabilitas->sum());

        $total_probabilitas->each(function ($value, $key) use ($sum, $chart_data, $data_penyakit) {
            $result = ($value / $sum) * 100;

            // chart data
            if ($result) {
                $chart_data->push([
                    'kode' => $data_penyakit[$key]->kode,
                    'penyakit' => $data_penyakit[$key]->nama_penyakit,
                    'persentase' => round($result, 0),
                    'solusi' => $data_penyakit[$key]->solusi
                ]);
            }
        });

        return $chart_data;
    }

    private function normalisasiProbabilitas($total_probabilitas)
    {
        $normalisasi_probabilitas = collect([]);
        $sum = round($total_probabilitas->sum());

        $total_probabilitas->each(function ($value) use ($sum, $normalisasi_probabilitas) {
            $result = ($value / $sum) * 100;
            $normalisasi_probabilitas->push($result ? round($result, 0) . '%' : '-');
        });

        return $normalisasi_probabilitas;
    }
}
