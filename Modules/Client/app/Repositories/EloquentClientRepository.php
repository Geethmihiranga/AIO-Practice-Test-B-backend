<?php

namespace Modules\Client\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Client\Contracts\ClientRepositoryInterface;
use Modules\Client\Models\Client;
use Modules\Client\Services\ClientPricingCalculator;

class EloquentClientRepository implements ClientRepositoryInterface
{
    public function __construct(private readonly ClientPricingCalculator $pricingCalculator) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Client::query()->latest('id')->paginate($perPage);
    }

    public function find(int $id): ?Client
    {
        return Client::query()->find($id);
    }

    public function findByPhone(string $phone): ?Client
    {
        return Client::query()->where('phone', $phone)->first();
    }

    public function create(array $data): Client
    {
        $pricing = $this->pricingCalculator->calculate(
            $data['project_budget'],
            $data['company_employee_count'],
        );

        return Client::query()->create([...$data, ...$pricing]);
    }

    public function update(Client $client, array $data): Client
    {
        $client->fill($data);
        $client->fill($this->pricingCalculator->calculate(
            $client->project_budget,
            $client->company_employee_count,
        ));
        $client->save();

        return $client->refresh();
    }

    public function delete(Client $client): void
    {
        $client->delete();
    }
}
