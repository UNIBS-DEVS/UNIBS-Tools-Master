<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Get all users
     */
    public function index()
    {
        $users = User::select(
            'id',
            'name',
            'email',
            'roles',
            'status'
        )
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                return [
                    'emp_id'     => $user->id,
                    'emp_name'   => $user->name,
                    'emp_email'  => $user->email,
                    'emp_role'   => is_array($user->roles)
                        ? implode(', ', $user->roles)
                        : $user->roles,
                    'status'     => ucfirst($user->status),
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Users fetched successfully.',
            'data'    => $users,
        ], 200);
    }
}
