<?php

namespace App\Http\Controllers;

use App\Http\Requests\GejalaRequest;
use App\Models\Gejala;
use App\Services\GejalaService;
use Inertia\Inertia;

class GejalaController extends Controller
{
    private GejalaService $gejalaService;

    public function __construct(GejalaService $gejalaService)
    {
        $this->gejalaService = $gejalaService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gejalas = $this->gejalaService->getAll();

        return Inertia::render('gejala/index', ['gejalas' => $gejalas]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GejalaRequest $request)
    {

        $gejala = $this->gejalaService->create($request->validated());

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gejala $gejala)
    {
        return Inertia::render('gejala/edit', ['gejala' => $gejala]);
    }

    public function show()
    {
        return redirect()->route('gejala.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GejalaRequest $request, Gejala $gejala)
    {
        $gejala->update($request->validated());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gejala $gejala)
    {
        $gejala->delete();
        return back();
    }
}
