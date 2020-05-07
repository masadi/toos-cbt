<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User_question;
use App\User_exam;
class KonversiUjian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'konversi:ujian';

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
        $user_questions = User_question::with('peserta_didik.user')->where(function($query){
            $query->whereNull('user_id');
            $query->whereNull('ptk_id');
        })->orderBy('user_question_id')->get();
        if($user_questions->count()){
            $a=1;
            foreach($user_questions as $user_question){
                $user_id = $user_question->peserta_didik->user->user_id;
                $this->info('Memproses user_question ke => '.$a);
                $user_question->user_id = $user_id;
                $user_question->save();
                $a++;
            }
        }
        $user_exams = User_exam::with(['peserta_didik.user', 'user_question'])->where(function($query){
            $query->whereNull('user_id');
            $query->whereNull('ptk_id');
        })->get();
        if($user_exams->count()){
            $a=1;
            foreach($user_exams as $user_exam){
                $user_id = $user_exam->peserta_didik->user->user_id;
                $this->info('Memproses user_exam ke => '.$a);
                $user_exam->user_id = $user_id;
                $user_exam->save();
                $a++;
            }
        }
        $this->info('Proses Konversi user_questions Sempurna!!!');
        $this->info('Proses Konversi user_exam Sempurna!!!');
    }
}
