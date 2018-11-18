<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessRequestJob;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth; 
use Validator;

class PassportController extends Controller
{
    //
    public $successStatus = 200;
 
 
   /**
    * login api
    *
    * @return \Illuminate\Http\Response
    */
 
    public function getToken(){
       
       if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
 
           $user = Auth::user();
           $success['token'] =  $user->createToken('MyApp')->accessToken;
           return response()->json(['success' => $success], $this->successStatus);
 
       }else{
 
           return response()->json(['error'=>'Unauthorised'], 401);
       }
    }
// 
//   /**
//    * Register api
//    *
//    * @return \Illuminate\Http\Response
//    */
// 
//    public function register(Request $request){
// 
//       $validator = Validator::make($request->all(), [
//            'name' => 'required',
//            'email' => 'required|email',
//            'password' => 'required',
//            'c_password' => 'required|same:password',
//       ]);
//       
//        if ($validator->fails()) {
//
//            return response()->json(['error'=>$validator->errors()], 401);
//        }
//       
//        $input = $request->all();
//        $input['password'] = bcrypt($input['password']);
//        $user = User::create($input);
//        $success['token'] =  $user->createToken('MyApp')->accessToken;
//        $success['name'] =  $user->name;
//
//        return response()->json(['success'=>$success], $this->successStatus);
//    }
 
   /**
    * receive request
    *
    * @return \Illuminate\Http\Response
    */
 
    public function receiveRequest(Request $request){
        $rq_id= uniqid();
        
        Log::info("request : ".request('data')." [$rq_id]");//log request
        $dispatch=ProcessRequestJob::dispatch(request('data'))->onQueue('chomoka')->delay(5);//dispatch job onto a queue name chomoka with 5 seconds processing delay
        
        if($dispatch){
            
            Log::info("dispatched [$rq_id] ");
        }else{
            
            Log::info("dispatch failure [$rq_id] ");
        }
        
        return response()->json(['success' => 'OK'], $this->successStatus);
    }
}
