<?php

use Illuminate\Database\Seeder;

class Mst_wilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		//DB::table('mst_wilayah')->truncate();
		//DB::table('negara')->truncate();
        //DB::table('level_wilayah')->truncate();
		Eloquent::unguard();
		$sql = File::get('database/data/level_wilayah.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/negara.sql');
        DB::unprepared($sql);
		for($i=1;$i<=8;$i++){
            $json = File::get('database/data/mst_wilayah_'.$i.'.json');
            $data = json_decode($json);
            foreach($data as $obj){
                DB::table('mst_wilayah')->insert([
                'kode_wilayah' 			=> $obj->kode_wilayah,
                'nama' 			=> $obj->nama,
                'id_level_wilayah'				=> $obj->id_level_wilayah,
                'mst_kode_wilayah'				=> $obj->mst_kode_wilayah,
                'negara_id'				=> $obj->negara_id,
                'asal_wilayah'				=> $obj->asal_wilayah,
                'kode_bps'			=> $obj->kode_bps,
                'kode_dagri'	=> $obj->kode_dagri,
                'kode_keu'			=> $obj->kode_keu,
                'created_at' 			=> $obj->create_date,
                'updated_at' 			=> $obj->last_update,
                'deleted_at'			=> $obj->expired_date,
                ]);
            }
        }
    }
}
