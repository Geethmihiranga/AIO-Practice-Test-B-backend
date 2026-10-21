<?php

namespace Modules\Client\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Client\Models\Client;

interface ClientRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Client;

    public function findByPhone(string $phone): ?Client;

    public function create(array $data): Client;

    public function update(Client $client, array $data): Client;

    public function delete(Client $client): void;
}
