<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(LaratrustSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(AgamaSeeder::class);
        $this->call(JurusanSeeder::class);
        $this->call(KurikulumSeeder::class);
        $this->call(MataPelajaranSeeder::class);
        $this->call(Mata_pelajaran_kurikulumSeeder::class);
        $this->call(Mst_wilayahSeeder::class);
        $this->call(SemesterSeeder::class);
        $this->call(BentukPendidikanSeeder::class);
        $this->call(JenjangPendidikanSeeder::class);
        $this->call(TingkatPendidikanSeeder::class);
        //$this->call('UsersAndNotesSeeder::class);
        //$this->call('MenusTableSeeder::class);
    }
}
