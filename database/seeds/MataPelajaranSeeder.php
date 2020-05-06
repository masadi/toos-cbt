<?php

use Illuminate\Database\Seeder;

class MataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //DB::table('mata_pelajaran')->truncate();
		$json = File::get('database/data/mata_pelajaran.json');
		$data = json_decode($json);
        foreach($data as $obj){
			DB::table('mata_pelajaran')->insert([
				'mata_pelajaran_id' 	=> $obj->mata_pelajaran_id,
				'nama' 					=> $obj->nama,
				'pilihan_sekolah'		=> $obj->pilihan_sekolah,
				'pilihan_buku' 			=> $obj->pilihan_buku,
				'pilihan_kepengawasan'	=> $obj->pilihan_kepengawasan,
				'pilihan_evaluasi'		=> $obj->pilihan_evaluasi,
				'jurusan_id'			=> $obj->jurusan_id,
				'created_at'			=> $obj->created_at,
				'updated_at'			=> $obj->updated_at,
				'deleted_at'			=> $obj->deleted_at,
			]);
    	}
    }
}
