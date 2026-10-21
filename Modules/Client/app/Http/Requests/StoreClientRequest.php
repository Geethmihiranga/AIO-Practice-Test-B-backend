<?php

namespace Modules\Client\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:clients,email'],
            'phone' => ['required', 'string', 'max:30', 'unique:clients,phone'],
            'company' => ['required', 'string', 'max:255'],
            'company_employee_count' => ['required', 'integer', 'min:0'],
            'address' => ['required', 'string', 'max:5000'],
            'project_budget' => [
                'required',
                'numeric',
                'min:0.01',
                'max:9999999999999.99',
                'decimal:0,2',
            ],
        ];
    }
}
