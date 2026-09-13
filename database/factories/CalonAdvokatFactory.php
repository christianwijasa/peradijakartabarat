<?php

namespace Database\Factories;

use App\Models\CalonAdvokat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalonAdvokatFactory extends Factory
{
    protected $model = CalonAdvokat::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'kode_ca' => 'CA-'.date('Y').'-'.fake()->numerify('####'),
            'nik' => fake()->numerify('################'),
            'universitas' => fake()->randomElement(['Universitas Indonesia', 'Universitas Trisakti', 'Universitas Tarumanagara']),
            'ipk' => fake()->randomFloat(2, 3.0, 4.0),
            'upa_gelombang' => 'Gelombang I '.date('Y'),
            'tahun_lulus_upa' => date('Y'),
            'status_keanggotaan' => 'aktif',
            'status_verifikasi' => 'terverifikasi',
            'masa_magang_bulan' => 24,
        ];
    }
}
