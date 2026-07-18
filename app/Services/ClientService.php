<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientService
{
    public function createClient(array $data): Client
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['owner_name'] ?: $data['company_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole('client');

            return Client::create([
                'user_id' => $user->id,
                'company_name' => $data['company_name'],
                'owner_name' => $data['owner_name'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'status' => 'active',
            ]);
        });
    }

    public function updateClient(Client $client, array $data): Client
    {
        $client->update($data);

        return $client;
    }

    public function toggleStatus(Client $client): Client
    {
        $client->update(['status' => $client->status === 'active' ? 'blocked' : 'active']);

        return $client;
    }
}
