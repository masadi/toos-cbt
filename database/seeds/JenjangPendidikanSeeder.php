<?php

use Illuminate\Database\Seeder;

class JenjangPendidikanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/jenjang_pendidikan.json');
		$data = json_decode($json);
        foreach($data as $obj){
            DB::table('jenjang_pendidikan')->insert([
                'jenjang_pendidikan_id' => $obj->jenjang_pendidikan_id,
                'nama' => $obj->nama,
                'jenjang_lembaga' => $obj->jenjang_lembaga,
                'jenjang_orang' => $obj->jenjang_orang,
                'created_at' 			=> $obj->create_date,
                'updated_at' 			=> $obj->last_update,
            ]);
    	}
    }
}
