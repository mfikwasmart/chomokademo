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
        
        $lines = explode(PHP_EOL, $this->rq);//extract the csv request lines
        foreach ($lines as $line) {
            
            $temp= str_getcsv($line);
          
            $check=DB::table('movies')->where('movie_title', urldecode($temp[0]))->where('movie_url', urldecode($temp[2]))->pluck('id'); //check movie title and image url existance
           
            if(count($check) > 0){
                
                Log::info("movie exists : ".urldecode($temp[2]));
                continue;
            }
            
            $save=Movie::create(['movie_title' => urldecode($temp[0]),'movie_overview'=> urldecode($temp[1]),'movie_url'=> urldecode($temp[2])]); //save movie into the database
            if($save){
                
                Log::info("movie saved : ".urldecode($temp[0]));
                //send notification email to admin
                Mail::send('emails.notification', ['movie_title' => urldecode($temp[0])], function ($m){
                    $m->from('', 'Chomoka');

                    $m->to('', 'Administrator')->subject('Chomoka Notification!');
                });
            }else{

                Log::info("movie not saved : ".urldecode($temp[0]));
            }
        }
    }
}
