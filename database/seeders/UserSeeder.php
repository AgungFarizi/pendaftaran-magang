<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Manager
        User::create([
            'name' => 'Manager Tellinter',
            'email' => 'manager@tellinter.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'phone' => '081234567890',
        ]);

        // Manager Departemen - IT
        User::create([
            'name' => 'Manager IT Department',
            'email' => 'manager.it@tellinter.com',
            'password' => Hash::make('password'),
            'role' => 'manager_dept',
            'department' => 'IT',
            'phone' => '081234567891',
        ]);

        // Manager Departemen - HR
        User::create([
            'name' => 'Manager HR Department',
            'email' => 'manager.hr@tellinter.com',
            'password' => Hash::make('password'),
            'role' => 'manager_dept',
            'department' => 'HR',
            'phone' => '081234567892',
        ]);

        // Manager Departemen - Finance
        User::create([
            'name' => 'Manager Finance Department',
            'email' => 'manager.finance@tellinter.com',
            'password' => Hash::make('password'),
            'role' => 'manager_dept',
            'department' => 'Finance',
            'phone' => '081234567893',
        ]);

        // Operator
        User::create([
            'name' => 'Operator Tellinter',
            'email' => 'operator@tellinter.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'phone' => '081234567894',
        ]);

        User::create([
            'name' => 'Operator 2 Tellinter',
            'email' => 'operator2@tellinter.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'phone' => '081234567895',
        ]);

        // Pembimbing Lapang - IT
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'pembimbing.it@tellinter.com',
            'password' => Hash::make('password'),
            'role' => 'pembimbing',
            'department' => 'IT',
            'position' => 'Senior Software Engineer',
            'phone' => '081234567896',
        ]);

        // Pembimbing Lapang - HR
        User::create([
            'name' => 'Siti Rahayu',
            'email' => 'pembimbing.hr@tellinter.com',
            'password' => Hash::make('password'),
            'role' => 'pembimbing',
            'department' => 'HR',
            'position' => 'HR Specialist',
            'phone' => '081234567897',
        ]);

        // Sample Mahasiswa
        User::create([
            'name' => 'Ahmad Rizki',
            'email' => 'mahasiswa@tellinter.com',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'phone' => '081234567898',
            'university' => 'Universitas Indonesia',
            'major' => 'Teknik Informatika',
            'student_id' => '2021001001',
        ]);

        User::create([
            'name' => 'Dewi Putri',
            'email' => 'mahasiswa2@tellinter.com',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'phone' => '081234567899',
            'university' => 'Institut Teknologi Bandung',
            'major' => 'Sistem Informasi',
            'student_id' => '2021002002',
        ]);

        User::create([
            'name' => 'Eko Prasetyo',
            'email' => 'mahasiswa3@tellinter.com',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'phone' => '081234567800',
            'university' => 'Universitas Gadjah Mada',
            'major' => 'Ilmu Komputer',
            'student_id' => '2021003003',
        ]);
    }
}
