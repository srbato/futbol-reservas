<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipPlan extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'max_fields',
        'monthly_price',
        'annual_discount_percentage',
        'long_term_months',
        'trial_days',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function hasTrial(): bool
    {
        return (int) ($this->trial_days ?? 0) > 0;
    }

    public function longTermMonths(): int
    {
        return (int) ($this->long_term_months ?? 12);
    }

    public function longTermLabel(): string
    {
        return $this->longTermMonths() === 6 ? 'Semestral' : 'Anual';
    }

    public function annualTotalPrice(): float
    {
        $months = $this->longTermMonths();
        $total = (float) $this->monthly_price * $months;
        $discount = (float) $this->annual_discount_percentage / 100;
        return round($total * (1 - $discount), 2);
    }

    public function annualMonthlyEquivalent(): float
    {
        return round($this->annualTotalPrice() / $this->longTermMonths(), 2);
    }

    public function maxFieldsLabel(): string
    {
        return $this->max_fields ? "Hasta {$this->max_fields} canchas" : 'Canchas ilimitadas';
    }
}
