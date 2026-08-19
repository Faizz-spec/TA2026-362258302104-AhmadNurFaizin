<?php

namespace Database\Seeders;

use Arr;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Str;

class SimpusLoketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');
        $data = [];

        for ($i = 1; $i <= 20; $i++) {
            $umur = rand(1, 80);
            $kelUmur = $umur < 18 ? 1 : 2; // 1 = anak, 2 = dewasa (contoh)
            $statusPasien = $faker->randomElement(['bumil', 'anak sekolah', 'arpas', 'umum']);
            $jknPbi = $faker->boolean ? '1' : '0';

            $data[] = [
                'idLoket' => 'LOKET' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'pasienId' => rand(1, 5),
                'noUrut' => str_pad($i, 4, '0', STR_PAD_LEFT),
                'noKartu' => (string) rand(1000000000000, 9999999999999),
                'kdProvider' => 'PROV' . rand(100, 999),
                'statusKartu' => $faker->randomElement(['Aktif', 'Tidak Aktif']),
                'providerKartu' => $faker->company,
                'jnsPeserta' => $faker->randomElement(['PBI', 'Non PBI']),
                'kdTkp' => rand(1, 9),
                'noKunjungan' => 'KJ-' . rand(1000, 9999),
                'tglKunjungan' => date('Y-m-d'),
                'kunjSakit' => $faker->randomElement(['Ya', 'Tidak']),
                'kdPoli' => Arr::random([1, 2, 5,8]),
                'kdKegiatan' => rand(1, 10),
                'keluhan' => $faker->sentence(4),
                'puskId' => rand(1, 5),
                'unitId' => 1,
                'ukkId' => 'UKK' . rand(1, 10),
                'wilayah' => rand(1, 10),
                'kunjBaru' => $faker->randomElement(['Ya', 'Tidak']),
                'kelUmur' => $kelUmur,
                'umur' => $umur,
                'umur_bulan' => $faker->numberBetween(0, 11),
                'umur_hari' => $faker->numberBetween(0, 127),
                'jknPbi' => $jknPbi,
                'statusPasien' => $statusPasien,
                'createdDate' => now(),
                'createdBy' => 'Seeder',
                'modifiedDate' => now(),
                'modifiedBy' => 'Seeder',
                'kategoriUnitId' => rand(1, 3),
                'PROP' => 'PR' . rand(1, 99),
                'KAB' => 'KB' . rand(1, 99),
                'statusResponSatuData' => $faker->randomElement(['OK', 'Pending', 'Error']),
                'idResponSatuData' => Str::random(10),
                'timeRespon' => now(),
                'no_antrian' => str_pad($i, 3, '0', STR_PAD_LEFT),
                'resep_diambil' => $faker->randomElement(['0', '1']),
                'sample_resep' => $faker->randomElement(['0', '1']),
                'statusLayananLab' => $faker->randomElement(['0', '1']),
                'startTImeLab' => now(),
                'endTimeLab' => now(),
                'startTimeResep' => now(),
                'endTimeResep' => now(),
                'PHONE' => $faker->phoneNumber,
                'kdDokter' => 'DR' . rand(100, 999),
                'resp_add' => $faker->ipv4,
                'resp_panggil' => $faker->ipv4,
                'idResponRekamMedik' => Str::random(15),
            ];
        }

        DB::table('simpus_loket')->insert($data);
    }
}
