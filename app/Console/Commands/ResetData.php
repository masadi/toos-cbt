<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Server;
use App\Rombongan_belajar;
class ResetData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:data {username}';

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
        $server = Server::where('id_server', $username)->first();
        if($server){
            Rombongan_belajar::where('server_id', $server->server_id)->delete();
        }
    }
}
