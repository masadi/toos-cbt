<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Exam;
use App\Setting;
use Str;
use Delight\Random\Random;
class TokenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->exam_id = $exam_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $token = Random::alphaUppercaseHumanString(6);
        Setting::updateOrCreate(
            [
                'key' => 'token'
            ],
            [
                'value' => $token
            ]
        );
        Exam::whereAktif(1)->update(['token' => $token]);
        //$exam = Exam::find($this->exam_id);
        //$exam->token = $token;
        //$exam->save();
    }
}
