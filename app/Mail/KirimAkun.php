<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Anggota_rombel;
class KirimAkun extends Mailable
{
    use Queueable, SerializesModels;
    public $anggota;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Anggota_rombel $anggota)
    {
        $this->anggota = $anggota;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@cyberelectra.co.id')->view('proktor.kirim-akun');
    }
}
