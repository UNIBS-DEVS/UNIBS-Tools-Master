<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function createUser(array $data)
    {
        $password = $data['password'];

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'personal_mobile' => $data['personal_mobile'],
            'offical_mobile' => $data['offical_mobile'],
            'roles' => array_map(
                'strtolower',
                $data['roles']
            ),
            'status' => $data['status'],
            'manager_id' => $data['manager_id'],
            'password' => Hash::make($password),
        ]);

        return [
            'user' => $user,
            'password' => $password
        ];
    }

    public function updateUser(User $user, array $data): array
    {
        $passwordChanged = false;
        $emailChanged = false;

        if ($user->email !== $data['email']) {
            $emailChanged = true;
        }

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'personal_mobile' => $data['personal_mobile'],
            'offical_mobile' => $data['offical_mobile'],
            'roles' => array_map(
                'strtolower',
                $data['roles']
            ),
            'status' => $data['status'],
            'manager_id' => $data['manager_id'],
        ];

        $plainPassword = null;

        if (!empty($data['password'])) {

            $passwordChanged = true;

            $plainPassword = $data['password'];

            $updateData['password'] = Hash::make(
                $data['password']
            );
        }

        $user->update($updateData);

        return [
            'user' => $user->fresh(),
            'send_mail' => ($emailChanged || $passwordChanged),
            'plain_password' => $plainPassword,
        ];
    }
}
