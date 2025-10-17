<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenyakitRequest;
use App\Models\Penyakit;
use App\Services\PenyakitService;
use Inertia\Inertia;

class PenyakitController extends Controller
{
    private PenyakitService $penyakitService;

    public function __construct(PenyakitService $penyakitService)
    {
        $this->penyakitService = $penyakitService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penyakits = $this->penyakitService->getAll();

        return Inertia::render('penyakit/index', ['penyakits' => $penyakits]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PenyakitRequest $request)
    {
        $penyakit = $this->penyakitService->create($request->validated());

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penyakit $penyakit)
    {
        return Inertia::render('penyakit/edit', ['penyakit' => $penyakit]);
    }

    public function show()
    {
        return redirect()->route('penyakit.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PenyakitRequest $request, Penyakit $penyakit)
    {
        $penyakit->update($request->validated());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penyakit $penyakit)
    {
        $penyakit->delete();

        return back();
    }
}
