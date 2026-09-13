<?php

namespace Database\Factories;

use App\Models\LawFirm;
use Illuminate\Database\Eloquent\Factories\Factory;

class LawFirmFactory extends Factory
{
    protected $model = LawFirm::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->company().' Law Office',
            'alamat' => fake()->address(),
            'sk_kemenkumham' => 'AHU-'.fake()->numerify('######').'.AH.01.01',
            'kuota_maks' => 10,
            'status_verifikasi' => 'terverifikasi',
            'diverifikasi_pada' => now(),
        ];
    }
}
