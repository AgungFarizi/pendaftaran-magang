<?php

namespace Database\Seeders;

use App\Models\InternshipPeriod;
use Illuminate\Database\Seeder;

class InternshipPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InternshipPeriod::create([
            'name' => 'Program Magang Semester Genap 2024',
            'description' => 'Program magang untuk mahasiswa semester genap tahun akademik 2023/2024. Kesempatan untuk belajar dan berkembang bersama tim profesional Tellinter.',
            'start_date' => '2024-01-01',
            'end_date' => '2024-02-28',
            'quota' => 50,
            'requirements' => "1. Mahasiswa aktif minimal semester 5\n2. IPK minimal 3.00\n3. Bersedia magang selama 3-6 bulan\n4. Memiliki kemampuan komunikasi yang baik\n5. Mampu bekerja dalam tim",
            'status' => 'active',
        ]);

        InternshipPeriod::create([
            'name' => 'Program Magang Semester Ganjil 2024/2025',
            'description' => 'Program magang untuk mahasiswa semester ganjil tahun akademik 2024/2025.',
            'start_date' => '2024-07-01',
            'end_date' => '2024-08-31',
            'quota' => 75,
            'requirements' => "1. Mahasiswa aktif minimal semester 5\n2. IPK minimal 3.00\n3. Bersedia magang selama 3-6 bulan\n4. Memiliki kemampuan komunikasi yang baik\n5. Mampu bekerja dalam tim\n6. Menguasai bahasa Inggris (nilai plus)",
            'status' => 'draft',
        ]);

        InternshipPeriod::create([
            'name' => 'Program Magang Khusus IT 2024',
            'description' => 'Program magang khusus untuk mahasiswa jurusan IT/Informatika.',
            'start_date' => '2024-03-01',
            'end_date' => '2024-04-30',
            'quota' => 30,
            'requirements' => "1. Mahasiswa jurusan IT/Informatika/Sistem Informasi\n2. Menguasai minimal 1 bahasa pemrograman\n3. IPK minimal 3.25\n4. Memiliki portofolio project\n5. Bersedia magang minimal 4 bulan",
            'status' => 'active',
        ]);
    }
}
