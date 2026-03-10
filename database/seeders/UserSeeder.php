<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Laravolt\Indonesia\Models\Province;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 1. Akun Admin PP
        User::create([
            'name'     => 'Admin Pusat JPRMI',
            'email'    => 'pusat@jprmi.or.id',
            'password' => Hash::make('jprmi2026'),
            'role'     => 'Admin PP',
        ]);

        // 2. Akun Admin PW (DKI Jakarta)
        // Laravolt menggunakan 'name' untuk nama wilayah
        $provDKI = Province::where('name', 'LIKE', '%JAKARTA%')->first();

        if ($provDKI) {
            User::create([
                'name'        => 'Admin PW DKI Jakarta',
                'email'       => 'dki@jprmi.or.id',
                'password'    => Hash::make('jprmi2026'),
                'role'        => 'Admin PW',
                'provinsi_id' => $provDKI->id,
            ]);
        }
    }
}
