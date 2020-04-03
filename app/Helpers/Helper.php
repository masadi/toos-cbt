<?php

namespace App\Helpers;
use App\Semester;
class Helper
{
    public static function TanggalIndo($date){
		$BulanIndo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
		$tahun = substr($date, 0, 4);
		$bulan = substr($date, 5, 2);
		$tgl   = substr($date, 8, 2);
		$result = $tgl . " " . $BulanIndo[(int)$bulan-1] . " ". $tahun; 
		return($result);
	}
	public static function generateAlphabet($na) {
        $sa = "";
        while ($na >= 0) {
            $sa = chr($na % 26 + 65) . $sa;
            $na = floor($na / 26) - 1;
        }
        return $sa;
    }
    public static function UniqueMachineID($salt = "") {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $temp = sys_get_temp_dir().DIRECTORY_SEPARATOR."diskpartscript.txt";
            if(!file_exists($temp) && !is_file($temp)) file_put_contents($temp, "select disk 0\ndetail disk");
            $output = shell_exec("diskpart /s ".$temp);
            $lines = explode("\n",$output);
            $result = array_filter($lines,function($line) {
                return stripos($line,"ID:")!==false;
            });
            if(count($result)>0) {
                $result = end($result);
                $result = explode(":",$result);
                $result = trim(end($result));
                $result = str_replace('{', '', $result);
                $result = str_replace('}', '', $result);
            } else $result = $output;       
        } else {
            $result = shell_exec("blkid -o value -s UUID");
            if(stripos($result,"blkid")!==false) {
                $result = $_SERVER['HTTP_HOST'];
            }
        }
        return $result;
    }
    public static function internet(){
        $connected = @fsockopen("newtoos.cyberelectra.co.id", 80);//website, port  (try 80 or 443)
        if ($connected){
            $is_conn = true; //action when connected
            fclose($connected);
        } else {
            $is_conn = false; //action in connection failure
        }
        return $is_conn;
    }
    public static function semester(){
        $semester = Semester::find(config('global.semester_id'));
        return ($semester) ? $semester->nama : '-';
    }
    public static function clean($string) {
        $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
        $string = strtolower($string);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
    public static function player($html){
        $html = str_replace('controls="controls"','id="player"', $html);
        $html = str_replace('controlslist="nodownload"','ontimeupdate="initProgressBar()"', $html);
        $html = preg_replace('#<audio(.*)\s?/audio>#iU', '<div class="audio-wrapper" id="player-container" href="javascript:;"><audio$1 $2audio></div>
        <div class="player-controls scrubber">
            <span id="seek-obj-container">
                <a href="javascript:;" id="play-button" class="btn btn-secondary btn-lg active">
                    <i class="cil-media-play mr-2"></i>
                    <progress id="seek-obj" value="0" max="1"></progress>
                    <small style="float: left; position: relative; left: 15px; display:none;" id="start-time"></small>
                    <small style="float: right; position: relative; right: 20px; display:none;" id="end-time"></small>
                </a>
            </span>
        </div>', $html, -1);
        return $html;
    }
}
