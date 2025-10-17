<?php

namespace App\Http\Controllers;

use App\Http\Requests\RekapRequest;
use App\Models\Rekap;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use RekapService;

class RekapController extends Controller
{
    private RekapService $rekapService;

    public function __construct(RekapService $rekapService)
    {
        $this->rekapService = $rekapService;
    }

    public function index()
    {
        $userId = Auth::user()->id;
        $roleId = Auth::user()->role_id;

        if ($roleId == 2) {
            $rekaps = $this->rekapService->getByUserId($userId, $userId);
        } else {
            $fields = ['rekaps.*', 'users.name', 'users.email', 'roles.name as role'];
            $rekaps = $this->rekapService->getAllJoinUserAndRole($fields);
        }

        return Inertia::render('rekap/index', [
            'rekaps' => $rekaps,
            'role_id' => $roleId,
        ]);
    }

    public function store(RekapRequest $request)
    {
        try {
            $userId = Auth::user()->id;

            $this->rekapService->create($request->validated(), $userId);

            return redirect()->back();
        } catch (\Exception $e) {
            return back()->withErrors([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function destroy(Rekap $rekap)
    {
        $rekap->delete();
        return back();
    }
}
