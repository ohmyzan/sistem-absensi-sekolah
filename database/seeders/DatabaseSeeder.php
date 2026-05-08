<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Akun Admin Utama
        User::firstOrCreate(
            ['email' => 'admin@smkjakarta1.com'], // Cek agar tidak duplikat jika di-run 2x
            [
                'name' => 'Admin SMK 1',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 2. Buat Akun Dummy Siswa 1
        $siswa1 = User::firstOrCreate(
            ['email' => 'budi@siswa.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ]
        );
        Student::firstOrCreate(
            ['nis' => '19240001'],
            [
                'user_id' => $siswa1->id,
                'class' => '11 RPL 1',
                // photo dan face_descriptor dibiarkan kosong untuk diisi manual via web
            ]
        );

        // 3. Buat Akun Dummy Siswa 2
        $siswa2 = User::firstOrCreate(
            ['email' => 'siti@siswa.com'],
            [
                'name' => 'Siti Aminah',
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ]
        );
        Student::firstOrCreate(
            ['nis' => '19240002'],
            [
                'user_id' => $siswa2->id,
                'class' => '11 RPL 1',
            ]
        );
    }
}
