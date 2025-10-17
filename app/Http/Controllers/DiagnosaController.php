<?php

namespace App\Http\Controllers;

use App\Services\DiagnosaService;
use App\Services\GejalaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DiagnosaController extends Controller
{
    private GejalaService $gejalaService;
    private DiagnosaService $diagnosaService;

    public function __construct(
        GejalaService $gejalaService,
        DiagnosaService $diagnosaService
    )
    {
        $this->gejalaService = $gejalaService;
        $this->diagnosaService = $diagnosaService;
    }

    public function index()
    {
        $param = request()->get('back');

        if ($param == 1 || !session()->has('diagnosa')) {
            if (session()->has('diagnosa')) {
                session()->forget('diagnosa');
            }

            $gejalas = $this->gejalaService->getSelectRawAll("CONCAT('G', id) as kode_gejala, nama_gejala");

            return Inertia::render('diagnosa/index', [
                'gejalas' => $gejalas,
            ]);
        } else {
            return redirect()->route('diagnosa.hasil');
        }
    }

    public function prosesDiagnosa(Request $request)
    {
        // hasil disimpan ke session
        session()->put('diagnosa', $request->data);

        return redirect()->route('diagnosa.hasil');
    }

    public function showHasil()
    {
        // Ambil dari session
        $data = session()->get('diagnosa');

        if (!$data) {
            return redirect()->route('diagnosa'); // Kembali jika tidak ada data
        }

        $hasil = $this->diagnosaService->naiveBayes($data);

        return Inertia::render('diagnosa/hasil-diagnosa', [
            'hasil' => $hasil
        ]);
    }

}
