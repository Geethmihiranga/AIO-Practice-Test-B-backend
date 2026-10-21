<?php

namespace Modules\Client\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'company_employee_count' => $this->company_employee_count,
            'address' => $this->address,
            'project_budget' => $this->project_budget,
            'service_charge' => $this->service_charge,
            'discount_value' => $this->discount_value,
            'final_project_value' => $this->final_project_value,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
