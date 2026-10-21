<?php

namespace Modules\Client\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Client\Contracts\ClientRepositoryInterface;
use Modules\Client\Http\Requests\StoreClientRequest;
use Modules\Client\Http\Requests\UpdateClientRequest;
use Modules\Client\Http\Resources\ClientResource;
use Modules\Client\Models\Client;

class ClientController extends Controller
{
    public function __construct(private readonly ClientRepositoryInterface $clients) {}

    public function index()
    {
        return ClientResource::collection($this->clients->paginate());
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $client = $this->clients->findByPhone($validated['phone']);

        abort_if($client === null, 404, 'Client not found.');

        return ClientResource::make($client);
    }

    public function store(StoreClientRequest $request)
    {
        $client = $this->clients->create($request->validated());

        return ClientResource::make($client)->response()->setStatusCode(201);
    }

    public function show(int $client)
    {
        $client = $this->clients->find($client);

        abort_if($client === null, 404, 'Client not found.');

        return ClientResource::make($client);
    }

    public function update(UpdateClientRequest $request, int $client)
    {
        $client = $this->clients->find($client);

        abort_if($client === null, 404, 'Client not found.');

        $client = $this->clients->update($client, $request->validated());

        return ClientResource::make($client);
    }

    public function destroy(int $client)
    {
        $client = $this->clients->find($client);

        abort_if($client === null, 404, 'Client not found.');

        $this->clients->delete($client);

        return response()->noContent();
    }
}
