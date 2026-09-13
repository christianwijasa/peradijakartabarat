<?php

namespace Database\Factories;

use App\Models\AdvokatPendamping;
use App\Models\LawFirm;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdvokatPendampingFactory extends Factory
{
    protected $model = AdvokatPendamping::class;

    public function definition(): array
    {
        return [
            'law_firm_id' => LawFirm::factory(),
            'nama' => fake()->name().', S.H.',
            'kta_nomor' => 'KTA-DPC-JB-'.fake()->numerify('#####'),
            'kta_aktif' => true,
            'pengalaman_tahun' => fake()->numberBetween(5, 20),
        ];
    }
}
