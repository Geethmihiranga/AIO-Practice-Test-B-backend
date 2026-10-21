<?php

namespace Modules\Client\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Client\Models\Client;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $client = $this->route('client');
        $clientId = $client instanceof Client ? $client->getKey() : $client;

        return [
            'phone' => [
                'sometimes',
                'required',
                'string',
                'max:30',
                Rule::unique('clients', 'phone')->ignore($clientId),
            ],
            'address' => ['sometimes', 'required', 'string', 'max:5000'],
            'company_employee_count' => ['sometimes', 'required', 'integer', 'min:0'],
            'project_budget' => [
                'sometimes',
                'required',
                'numeric',
                'min:0.01',
                'max:9999999999999.99',
                'decimal:0,2',
            ],
        ];
    }
}
