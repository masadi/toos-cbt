<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Event;
use App\Exam;
use App\User;
use Illuminate\Support\Facades\Storage;
class GenerateUjian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:ujian {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $username = $this->argument('username');
        $event = Event::where('kode', $username)->with('peserta')->first();
        if($event){
            $all_ujian = Exam::with('event')->whereAktif(1)->whereHas('event')->get();
            foreach($event->peserta as $peserta){
                $all_user = User::where('sekolah_id', $peserta->sekolah_id)->whereNotNull('peserta_didik_id')->get();
                foreach($all_user as $user){
                    $this->info('Memperoses peserta '.$user->name);
                    foreach($all_ujian as $ujian){
                        $json_file_ujian = 'ujian-'.$user->user_id.'-'.$ujian->exam_id.'.json';
                        if(!Storage::disk('public')->exists($json_file_ujian)){
                            $this->info('Mulai proses File Peserta '.$user->name);
                            $get_ujian = Exam::withCount(['question', 'user_question' => function($query) use ($user){
                                $query->where('user_questions.user_id', $user->user_id);
                            }])->with(['question' => function($query){
                                $query->with('answers');
                                $query->orderBy('soal_ke');
                            }, 'user_exam' => function($query) use ($user){
                                $query->where('user_exams.user_id', $user->user_id);
                            }])->find($ujian->exam_id);
                            Storage::disk('public')->put($json_file_ujian, $get_ujian->toJson());
                        } //else {
                            //$this->info('File Peserta '.$user->name.' sudah sudah ada');
                        //}
                    }
                }
            }
        }
    }
}
