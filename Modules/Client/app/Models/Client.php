<?php

namespace Modules\Client\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'company_employee_count',
        'address',
        'project_budget',
        'service_charge',
        'discount_value',
        'final_project_value',
    ];

    protected function casts(): array
    {
        return [
            'company_employee_count' => 'integer',
            'project_budget' => 'decimal:2',
            'service_charge' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'final_project_value' => 'decimal:2',
        ];
    }
}
