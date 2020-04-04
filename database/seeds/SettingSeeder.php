<?php

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$url_server = 'http://portal.toos:8002/api/';
        //$url_server = 'http://newtoos.cyberelectra.co.id/api/';
        $url_server = 'https://portal.cyberelectra.co.id/api/';
        $all_data = array(
            array('key' => 'app_name', 'value' => 'TOOS (Tes Online Offline Sekolah)'),
			array('key' => 'app_version', 'value' => '3.0.0'),
			array('key' => 'db_version', 'value' => '2.0.0'),
            array('key' => 'semester_id', 'value' => 20201),
            array('key' => 'menu', 'value' => 0),
            array('key' => 'url_server', 'value' => $url_server),
		);
		//DB::table('settings')->truncate();
		foreach($all_data as $data){
			DB::table('settings')->insert($data);
		}
    }
}
