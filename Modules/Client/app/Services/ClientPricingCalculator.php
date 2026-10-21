<?php

namespace Modules\Client\Services;

use InvalidArgumentException;

class ClientPricingCalculator
{
    /**
     * Calculate persisted charge, discount, and final value in integer cents.
     *
     * @return array{service_charge: string, discount_value: string, final_project_value: string}
     */
    public function calculate(string|int|float $budget, int $employeeCount): array
    {
        $budgetInCents = $this->toCents($budget);

        $serviceRate = match (true) {
            $budgetInCents >= 100_000_000 => 10,
            $budgetInCents >= 50_000_000 => 5,
            default => 2,
        };

        $discountRate = match (true) {
            $employeeCount > 100 => 5,
            $employeeCount < 100 => 2,
            default => 0,
        };

        $serviceCharge = $this->percentage($budgetInCents, $serviceRate);
        $discount = $this->percentage($budgetInCents, $discountRate);

        return [
            'service_charge' => $this->fromCents($serviceCharge),
            'discount_value' => $this->fromCents($discount),
            'final_project_value' => $this->fromCents($budgetInCents + $serviceCharge - $discount),
        ];
    }

    private function toCents(string|int|float $amount): int
    {
        $value = is_float($amount)
            ? number_format($amount, 2, '.', '')
            : trim((string) $amount);

        if (! preg_match('/^\+?(?:\d+(?:\.\d{0,2})?|\.\d{1,2})$/', $value)) {
            throw new InvalidArgumentException('The project budget must be a non-negative decimal amount.');
        }

        $value = ltrim($value, '+');
        $value = str_starts_with($value, '.') ? "0{$value}" : $value;
        $value = str_ends_with($value, '.') ? substr($value, 0, -1) : $value;

        [$whole, $fraction] = array_pad(explode('.', $value, 2), 2, '0');

        return ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');
    }

    private function percentage(int $amountInCents, int $rate): int
    {
        return intdiv(($amountInCents * $rate) + 50, 100);
    }

    private function fromCents(int $amountInCents): string
    {
        return sprintf('%d.%02d', intdiv($amountInCents, 100), $amountInCents % 100);
    }
}
