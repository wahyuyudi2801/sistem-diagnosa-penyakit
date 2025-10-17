<?php

namespace App\Http\Controllers;

use DashboardService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $data = $this->dashboardService->getData();

        return Inertia::render('dashboard', ['data' => $data]);
    }
}
