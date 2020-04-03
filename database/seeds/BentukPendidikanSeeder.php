<?php

use Illuminate\Database\Seeder;

class BentukPendidikanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/bentuk_pendidikan.json');
		$data = json_decode($json);
        foreach($data as $obj){
            DB::table('bentuk_pendidikan')->insert([
                'bentuk_pendidikan_id' => $obj->bentuk_pendidikan_id,
                'nama' => $obj->nama,
                'jenjang_paud' => $obj->jenjang_paud,
                'jenjang_tk' => $obj->jenjang_tk,
                'jenjang_sd' => $obj->jenjang_sd,
                'jenjang_smp' => $obj->jenjang_smp,
                'jenjang_sma' => $obj->jenjang_sma,
                'jenjang_tinggi' => $obj->jenjang_tinggi,
                'direktorat_pembinaan' => $obj->direktorat_pembinaan,
                'aktif'   => $obj->aktif,
                'created_at' 			=> $obj->create_date,
                'updated_at' 			=> $obj->last_update,
            ]);
    	}
    }
}
