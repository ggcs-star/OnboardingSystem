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

            foreach ($data['products'] ?? [] as $productId) {

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
        $client->user()->update([
            'name' => $data['owner_name'] ?: $data['company_name'],
            'email' => $data['email'],
            ...(!empty($data['password']) ? ['password' => Hash::make($data['password'])] : []),
        ]);

        $client->update([
            'company_name' => $data['company_name'],
            'owner_name' => $data['owner_name'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'country' => $data['country'] ?? null,
        ]);

        return $client;
    }

    public function toggleStatus(Client $client): Client
    {
        $client->update(['status' => $client->status === 'active' ? 'blocked' : 'active']);

        return $client;
    }

    /**
     * Removes the client and its login account together — a Client row
     * without a User (or vice versa) is a dangling, unusable account.
     * Projects/client_products/etc. cascade-delete via their FK constraints.
     */
    public function deleteClient(Client $client): void
    {
        DB::transaction(function () use ($client) {
            $userId = $client->user_id;

            $client->delete();

            if ($userId) {
                User::destroy($userId);
            }
        });
    }
}
