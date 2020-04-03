<?php

use Illuminate\Database\Seeder;
//use database\seeds\UsersAndNotesSeeder;
//use database\seeds\MenusTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call('SettingSeeder');
        $this->call('AgamaSeeder');
        $this->call('JurusanSeeder');
        $this->call('KurikulumSeeder');
        $this->call('MataPelajaranSeeder');
        $this->call('Mata_pelajaran_kurikulumSeeder');
        $this->call('Mst_wilayahSeeder');
        $this->call('SemesterSeeder');
        $this->call('BentukPendidikanSeeder');
        $this->call('JenjangPendidikanSeeder');
        $this->call('TingkatPendidikanSeeder');
        $this->call('UsersAndNotesSeeder');
        $this->call('MenusTableSeeder');
        $this->command->line('Referensi Tambahan');
        Artisan::call('generate:data');
    }
}
