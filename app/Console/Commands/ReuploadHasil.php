<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User_exam;
class ReuploadHasil extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reupload:hasil';

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
        //$data = User_exam::with(['user','user_question'])->withCount('user_question')->find($request->user_exam_id);
        $count = User_exam::count();
        $limit = 5;
        $this->info('Jumlah Data => '.$count);
        for($i=0;$i<=$count;$i++){
            $this->info('Reupload ke => '.$i);
            if ( $bagi = $i % $limit == 0 ) {
                $data = User_exam::with(['user','user_question'])->withCount('user_question')->offset($i)->limit($limit)->orderBy('user_exam_id')->get();
                $this->call('proses:sync', ['query' => 'upload', 'data' => $data]);
            }
        }
    }
}
