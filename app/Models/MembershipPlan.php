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
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function annualTotalPrice(): float
    {
        $annual = (float) $this->monthly_price * 12;
        $discount = (float) $this->annual_discount_percentage / 100;
        return round($annual * (1 - $discount), 2);
    }

    public function annualMonthlyEquivalent(): float
    {
        return round($this->annualTotalPrice() / 12, 2);
    }

    public function maxFieldsLabel(): string
    {
        return $this->max_fields ? "Hasta {$this->max_fields} canchas" : 'Canchas ilimitadas';
    }
}
