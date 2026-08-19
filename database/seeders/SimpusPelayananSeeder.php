<?php

namespace Database\Seeders;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Testing\Fakes\Fake;
use Str;

class SimpusPelayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run(): void
    {
        $faker =  \Faker\Factory::create('id_ID');
        $lokets = DB::table('simpus_loket')->take(20)->get();

        foreach ($lokets as $loket) {
            $start = Carbon::today()->addHours(rand(7, 9))->addMinutes(rand(0, 59));
            $progress = (clone $start)->addMinutes(rand(5, 20));
            $end = (clone $progress)->addMinutes(rand(5, 30));

            DB::table('simpus_pelayanan')->insert([
                'idpelayanan' => (string) Str::uuid(),
                'tglPelayanan' => now()->toDateString(),
                'pelIdSebelum' => null,
                'loketId' => $loket->idLoket,
                'kdPoli' => $loket->kdPoli ,
                'kdKegiatanPel' => str_pad($faker->numberBetween(1, 9), 3, '0', STR_PAD_LEFT),
                'kunjSakitPel' => $faker->randomElement(['Ya', 'Tidak']),
                'kdStatusPulang' => $faker->numberBetween(1, 3),
                'kdStatusPasienAskep' => $faker->numberBetween(0, 1),
                //'tujuanPoli' => str_pad($faker->numberBetween(1, 5), 3, '0', STR_PAD_LEFT),
                'tujuanKdKeg' => str_pad($faker->numberBetween(1, 9), 3, '0', STR_PAD_LEFT),
                'tujuanKunjSakit' => $faker->randomElement(['Ya', 'Tidak']),
                'tglPindah' => null,
                'sudahDilayani' => 0,
                'kdTacc' => null,
                'alasanTacc' => $faker->optional()->sentence(),
                'createdDate' => now(),
                'createdBy' => 'Seeder',
                'modifiedDate' => now(),
                'modifiedBy' => 'Seeder',
                'noKunjungan' => $loket->noKunjungan ?? ('KJ-' . $faker->numerify('####')),
                'id_encounter' => Str::random(10),
                'tenagaMedis' => $loket->tenagaMedisApotek ?? $faker->name(),
                'startTime' => $start,
                'progressTime' => $progress,
                'endTIme' => $end,
                'responPutEncounter' => $faker->randomElement(['v1', 'v2', 'v3']),
            ]);
        }
    }
}
