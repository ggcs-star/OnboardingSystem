<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\ClientProduct;
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

            $client = Client::create([
                'user_id' => $user->id,
                'company_name' => $data['company_name'],
                'owner_name' => $data['owner_name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'status' => 'active',
            ]);

            foreach ($data['products'] as $productId) {

                ClientProduct::create([
                    'client_id' => $client->id,
                    'product_id' => $productId,
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                    'status' => true,
                ]);
            }

            return $client;
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
