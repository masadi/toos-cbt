<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\User_question;
use File;
class KonversiFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'konversi:file';

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
        $all_files = Storage::disk('public')->files();
        $collection = collect($all_files)->filter(function ($item) {
            // replace stristr with your choice of matching function
            return false !== stristr($item, 'user_question');
        });
        $count = $collection->count();
        $lipat = 10;
        if (!File::isDirectory(public_path('done'))) {
            //MAKA FOLDER TERSEBUT AKAN DIBUAT
            File::makeDirectory(public_path('done'));
        }
        for($i=0;$i<=$count;$i++){
            $this->info('Memproses file ke => '.$i);
            if ( $bagi = $i % $lipat == 0 ) {
                $slice = $collection->slice($i, $lipat);
                foreach($slice->all() as $file){
                    $user_question = Storage::disk('public')->get($file);
                    $user_question = json_decode($user_question);
                    try {
                        User_question::updateOrCreate(
                            [
                                'question_id' => $user_question->question_id,
                                'anggota_rombel_id' => $user_question->anggota_rombel_id,
                                'ptk_id' => $user_question->ptk_id,
                            ],
                            [
                                'user_exam_id' => $user_question->user_exam_id,
                                'answer_id' => $user_question->answer_id,
                                'ragu' => $user_question->ragu,
                                'nomor_urut' => $user_question->nomor_urut,
                            ]
                        );
                    } catch (\Exception $e) {
                        $this->info('Gagal Memproses file ke => '.$i);
                    }
                    File::move(public_path('public/'.$file),public_path('done/'.$file));
                }
            }
        }
    }
}
