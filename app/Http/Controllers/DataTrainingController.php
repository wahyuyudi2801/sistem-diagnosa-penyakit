<?php

namespace App\Http\Controllers;

use App\Http\Requests\KeputusanRequest;
use App\Services\GejalaService;
use App\Services\KeputusanService;
use App\Services\PenyakitService;
use Exception;
use Inertia\Inertia;

class DataTrainingController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * dt => data table
     *
     */
    public function index()
    {
        // data dari model penyakit
        $penyakits = $this->penyakitService->getSelectRawAll("id, CONCAT('P', id) as kode");

        // data dari model keputusan
        $keputusans = $this->keputusanService->getSelectRawAll("id, CONCAT('P', penyakit_id) as kode_penyakit, CONCAT('G', gejala_id) as kode_gejala");

        // data dari model gejala
        $gejalas = $this->gejalaService->getSelectRawAll("id, CONCAT('G', id) as kode");

        // buat data table keputusan untuk ditampilkan di view
        $dt_keputusans = $this->keputusanService->getDatatable($gejalas, $penyakits, $keputusans);

        return Inertia::render('keputusan/index', [
            'dt_keputusans' => $dt_keputusans,
            'penyakits' => $penyakits
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // data dari model penyakit
        $penyakits = $this->penyakitService->getSelectRawAll("id, CONCAT('P', id) as kode, nama_penyakit");

        // data dari model keputusan join gejala
        $keputusans = $this->keputusanService->getSelectRawLeftJoinGejala("keputusans.id, gejala_id, penyakit_id, CONCAT('P', penyakit_id) as kode_penyakit, CONCAT('G', gejala_id) as kode_gejala, gejalas.nama_gejala");

        // data dari model gejala
        $gejalas = $this->gejalaService->getSelectRawAll("id, CONCAT('G', id) as kode_gejala, nama_gejala");

        return Inertia::render('keputusan/create', [
            'penyakits' => $penyakits,
            'keputusans' => $keputusans,
            'gejalas' => $gejalas,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KeputusanRequest $request)
    {
        try {
            $penyakitId = $request->validated()['penyakit_id'];
            $dataReq = $request->validated()['data'];
            $keputusan = $this->keputusanService->getByPenyakitId($penyakitId);

            if ($keputusan) {
                // hapus semua data keputusan yang ada
                $this->keputusanService->deleteByPenyakitId($penyakitId);

                // simpan data keputusan
                $this->keputusanService->creates($dataReq, $penyakitId);
            } else {
                // apakah penyakit_id ada di table penyakit
                $penyakit = $this->penyakitService->getById($penyakitId);

                // jika penyakit tidak ada
                if (!$penyakit) {
                    // buat data penyakit baru
                    $penyakit = $this->penyakitService->create([
                        'nama_penyakit' => $penyakitId, // penyakit id => nama baru
                        'solusi' => $request->validated()['solusi'],
                    ]);
                }

                // simpan data keputusan
                $this->keputusanService->creates($dataReq, $penyakit->id);
            }

            return back();
        } catch (Exception $e) {
            return back()->with('message', $e->getMessage());
        }
    }
}
