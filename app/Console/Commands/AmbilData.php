<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Event;
use App\Server;
class AmbilData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ambil:data {username} {data} {offset}';

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
        $data = $this->argument('data');
        $offset = $this->argument('offset');
        $event = Event::where('kode', $username)->with('peserta.sekolah')->first();
        if($event){
            $host_server = config('global.url_server').'proses-download-event';
            $arguments = [
                'data' => $data,
                'offset' => $offset,
                'event_id' => $event->id,
            ];
            $client = new Client(); //GuzzleHttp\Client
            $curl = $client->post($host_server, [	
                'curl.options' => [
                    'CURLOPT_BUFFERSIZE' => '120000L'
                ],
                ['verify' => false],
                'form_params' => $arguments
            ]);
            if($curl->getStatusCode() == 200){
                $output = json_decode($curl->getBody());
                $this->call('proses:sync', ['query' => 'download', 'data' => $output]);
                sleep(1);
                if($output){
                    if($output->response['next']){
                        $this->info('Start again => ambil:data '.$username.' '.$output->response['next'].' '.$output->response['offset']. ' Jumlah ('.$output->response['count'].')');
                        $this->call('ambil:data', ['username' => $username, 'data' => $output->response['next'],'offset' => $output->response['offset']]);
                    }
                }
            }
        } else {
            $server = Server::where('id_server', $username)->first();
            $host_server = config('global.url_server').'proses-download';
            $arguments = [
                'data' => $data,
                'offset' => $offset,
                'server_id' => $server->server_id,
            ];
            $client = new Client(); //GuzzleHttp\Client
            $curl = $client->post($host_server, [
                'curl.options' => [
                    'CURLOPT_BUFFERSIZE' => '120000L'
                ],
                ['verify' => false],
                'form_params' => $arguments
            ]);
            if($curl->getStatusCode() == 200){
                $output = json_decode($curl->getBody());
                $this->call('proses:sync', ['query' => 'download', 'data' => $output]);
                sleep(1);
                if($output){
                    if($output->response['next']){
                        $this->info('Start again => ambil:data '.$username.' '.$output->response['next'].' '.$output->response['offset']. ' Jumlah ('.$output->response['count'].')');
                        $this->call('ambil:data', ['username' => $username, 'data' => $output->response['next'],'offset' => $output->response['offset']]);
                    }
                }
            }
        }
    }
}
