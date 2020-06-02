<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
class ResetPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:password';

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
        $password = '12345678';
        $users = User::whereRoleIs('peserta_didik')->get();
        foreach($users as $user){
            $user->password = app('hash')->make($password);
            $user->default_password = $password;
            $user->save();
        }
    }
}
