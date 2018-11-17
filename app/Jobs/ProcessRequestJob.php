<?php

namespace App\Jobs;

use Mail;
Use Illuminate\Support\Facades\DB;
use App\Movie;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $rq;
    
    /**
     * Create a new job instance.
     *
     * @return void
    */
    public function __construct($request)
    {
        //
        $this->rq=$request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){
        
        $lines = explode(PHP_EOL, $this->rq);
        foreach ($lines as $line) {
            
            $temp= str_getcsv($line);
          
            $check=DB::table('movies')->where('movie_title',$temp[0])->where('movie_url', $temp[2])->pluck('id'); //check movie title existance
           
            if(count($check) > 0){
                
                Log::info("movie exists : ".$temp[2]);
                continue;
            }
            
            $save=Movie::create(['movie_title' => $temp[0],'movie_overview'=>$temp[1],'movie_url'=>$temp[2]]);
            if($save){
                
                Log::info("movie saved : ".$temp[0]);
                //send notification email to admin
                Mail::send('emails.notification', ['movie_title' => $temp[0]], function ($m){
                    $m->from('admin@smartcodes.co.tz', 'Chomoka Administrator');

                    $m->to('mfikwa@smartcodes.co.tz', 'Administrator')->subject('Chomoka Notification!');
                });
            }else{

                Log::info("movie not saved : ".$temp[0]);
            }
        }
    }
}
