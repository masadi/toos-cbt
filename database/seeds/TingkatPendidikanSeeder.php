<?php

use Illuminate\Database\Seeder;
class TingkatPendidikanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/tingkat_pendidikan.json');
		$data = json_decode($json);
        foreach($data as $obj){
            DB::table('tingkat_pendidikan')->insert([
                'tingkat_pendidikan_id' => $obj->tingkat_pendidikan_id,
                'kode' => $obj->kode,
                'nama' => $obj->nama,
                'jenjang_pendidikan_id' => $obj->jenjang_pendidikan_id,
                'created_at' 			=> $obj->create_date,
                'updated_at' 			=> $obj->last_update,
            ]);
    	}
    }
}
