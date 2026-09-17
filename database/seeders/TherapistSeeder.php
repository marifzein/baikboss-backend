<?php

namespace Database\Seeders;

use App\Models\Therapist;
use Illuminate\Database\Seeder;

class TherapistSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            // Terapis pria (massage pria)
            ['name' => 'Bagus', 'gender' => 'male', 'experience_years' => 5, 'rating' => 4.9, 'bio' => 'Spesialis massage deep tissue dan urut tradisional.'],
            ['name' => 'Rudi', 'gender' => 'male', 'experience_years' => 3, 'rating' => 4.8, 'bio' => 'Terapis pijat refleksi dan sports massage.'],
            ['name' => 'Dedi', 'gender' => 'male', 'experience_years' => 8, 'rating' => 4.9, 'bio' => 'Ahli pijat urut keluarga, ramah dan tepat waktu.'],

            // Terapis wanita (massage & facial wanita)
            ['name' => 'Sari', 'gender' => 'female', 'experience_years' => 6, 'rating' => 4.9, 'bio' => 'Spesialis facial dan perawatan kulit wajah.'],
            ['name' => 'Ratna', 'gender' => 'female', 'experience_years' => 4, 'rating' => 4.8, 'bio' => 'Terapis massage aromatherapy dan relaksasi.'],
            ['name' => 'Wulan', 'gender' => 'female', 'experience_years' => 7, 'rating' => 5.0, 'bio' => 'Ahli facial totok wajah dan glow treatment.'],
        ];

        foreach ($rows as $row) {
            Therapist::updateOrCreate(
                ['name' => $row['name'], 'gender' => $row['gender']],
                ['bio' => $row['bio'], 'experience_years' => $row['experience_years'], 'rating' => $row['rating'], 'active' => true],
            );
        }
    }
}
