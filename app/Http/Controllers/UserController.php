<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Jobs\User\SendUserAccountMail;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function filter(Request $request)
    {
        session([
            'user_filters' => $request->only([
                'name',
                'email',
                'personal_mobile',
                'offical_mobile',
                'role',
                'status',
            ])
        ]);

        return redirect()->route('users.index');
    }

    public function resetFilter()
    {
        session()->forget('user_filters');

        return redirect()->route('users.index');
    }

    public function index()
    {
        $filters = session('user_filters', []);

        $query = User::with('manager')->latest();

        // Name Filter
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        // Email Filter
        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        // Personal Mobile Filter
        if (!empty($filters['personal_mobile'])) {
            $query->where('personal_mobile', 'like', '%' . $filters['personal_mobile'] . '%');
        }

        // Official Mobile Filter
        if (!empty($filters['offical_mobile'])) {
            $query->where('offical_mobile', 'like', '%' . $filters['offical_mobile'] . '%');
        }

        // Role Filter (JSON)
        if (!empty($filters['role'])) {
            $query->whereJsonContains(
                'roles',
                strtolower($filters['role'])
            );
        }

        // Status Filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $users = $query->paginate(10);

        $allRoles = [
            'admin',
            'manager',
            'api user',
            'accounts',
            'customer',
            'db inspection',
        ];

        return view('users.index', compact(
            'users',
            'filters',
            'allRoles'
        ));
    }

    public function create()
    {
        $managers = User::whereJsonContains('roles', 'admin')
            ->orWhereJsonContains('roles', 'manager')
            ->get();

        return view('users.create', compact('managers'));
    }

    public function store(StoreUserRequest $request, UserService $service)
    {
        $result = $service->createUser(
            $request->validated()
        );

        SendUserAccountMail::dispatch(
            $result['user'],
            $result['password'],
            false
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        $managers = User::where(function ($q) {
            $q->whereJsonContains('roles', 'admin')
                ->orWhereJsonContains('roles', 'manager');
        })
            ->where('id', '!=', $user->id)
            ->get();

        return view('users.edit', compact(
            'user',
            'managers',
        ));
    }

    public function show($id)
    {
        $user = User::with('manager')->findOrFail($id);

        return view('users.show', compact(
            'user',
        ));
    }

    public function update(UpdateUserRequest $request, User $user, UserService $service)
    {
        // dd($request);
        $result = $service->updateUser(
            $user,
            $request->validated()
        );


        if ($result['send_mail']) {

            SendUserAccountMail::dispatch(
                $result['user'],
                $result['plain_password'],
                true
            );
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting own account
        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
