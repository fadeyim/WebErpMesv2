<?php

namespace Database\Factories\Companies;

use App\Models\Companies\Companies;
use App\Models\Companies\SupplierRating;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierRatingFactory extends Factory
{
    protected $model = SupplierRating::class;

    public function definition()
    {
        return [
            'purchases_id' => 1, // Exemple d'ID d'achat
            'companies_id' => Companies::all()->random()->id, // Exemple d'ID de compagnie
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->sentence,
        ];
    }
}
