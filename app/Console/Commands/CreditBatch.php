<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Koza;
use Carbon\Carbon;

class CreditBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'batch:credit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'クレジットの引き落とし処理';

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
        echo('クレジットバッチ開始します\n');
        $today = new Carbon(Carbon::now());
        $day = $today->format('d');
        $kozas = Koza::where('is_credit', '1')
               ->where('credit_date', $day)->get();
        foreach ($kozas as $koza) {
            $hiki = Koza::where('id', $koza->credit_koza_id)->where('user_id',$koza->user_id)->first();
            if(isset($hiki)){
                $hiki->zandaka = $koza->zandaka;
                $hiki->save();
                $koza->zandaka = 0;
                $koza->save();
            }
        }
        echo('クレジットバッチ終了します\n');
    }
}
