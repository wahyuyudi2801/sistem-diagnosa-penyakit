<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $fields = ['id', 'name', 'email', 'role_id'];
        $users = $this->userService->getAll($fields);

        return Inertia::render('user/index', ['users' => $users]);
    }

    public function store(UserRequest $request)
    {
        $user = $this->userService->create($request->validated());

        return back();
    }

    public function edit(User $user)
    {
        return Inertia::render('user/edit', ['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'role_id' => 'required'
        ]);

        if ($request->password) {
            $request->validate([
                'password' => 'required|min:8|confirmed',
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $user->save();

        return redirect()->back();
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back();
    }
}
