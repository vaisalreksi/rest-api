<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Exception;
use App\Helper\Helper;
use DB;
use Carbon\Carbon;
use App\Models\Module\SpkHeader;

class Spk extends Command
{
    protected $signature = 'notification:spk';
    protected $helper;

    protected $description = 'process send email spk';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
       parent::__construct();
       $this->helper = new Helper;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {

      $this->cekSpk();

    }

    public function cekSpk()
    {

      $data = array(
        'ref' => 'REF123',
        'date' => Carbon::now()->format('d M Y'),
        'name' => 'melak cangkeng'
      );

      $this->sendEmail($data,'aldihardiansyah.12@gmail.com','Expired SPK NO REF : 123');

      // $now = Carbon::today();
      // $paramDate = $now->addDays(10)->format('Y-m-d');
      // $spk = SpkHeader::where('end_date',$paramDate)->get();
      // if(!$spk->isEmpty()){
      //   foreach ($spk as $value) {
      //     if(isset($value->customerCommitment) && !empty($value->customerCommitment->email)){
      //         $data = array(
      //           'ref' => $value->no_ref,
      //           'date' => Carbon::parse($value->end_date)->format('d M Y'),
      //           'name' => $value->customerCommitment->name
      //         );
      //
      //         $this->sendEmail($data,$value->customerCommitment->email,'Expired SPK NO REF : '.$value->no_ref);
      //     }
      //   }
      // }

    }


    public function sendEmail($data,$email,$subject)
    {

      $status = "berhasil";
      $this->helper->sendEmail($data,$email,$subject,'mail.mail');
      return $status;
    }
}
