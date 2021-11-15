<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Business;
use App\Models\Exchange;
use App\Models\Notification;
use App\Models\User;
use App\Models\Rate;

use Validator;
use Carbon\Carbon;
use Goutte\Client;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

//add use of all needed models


class buyerController extends Controller
{   
    public function getUserprofile(){
        $business_id =  auth()->user()->id;
        $business = Business::find($business_id);
        $response['name'] =  $business->name;
        $response['picture_url'] =  $business->picture_url;
        $response['phone_number'] =  $business->phone_number;
        $response['bio']= $business->bio;
         $email = User::find($business_id)->email;
         $response['email']= $email;
         $response['expoToken']= User::find($business_id)->expoToken;
        return response()->json($response, 200);
    }


    function addPicture(Request $request){
        $image = $request->image;  // your base64 encoded
        $imageName = "str_random(".rand(10,1000).")".'.'.'jpeg';
        $path=public_path();
        \File::put($path. '/image/' . $imageName, base64_decode($image));
        $user_id = auth()->user()->id;
        $user = Business::find($user_id);
        $user->picture_url = '/image/'.$imageName;
        $user->save();
        return response()->json('hi', 200);
    }

    function editApi(Request $request){  
        $business_id = auth()->user()->id;     
        $business = Business::find($business_id);
        $business->name = $request->name;
        $business->save();
        $business = User::find($business_id); 
        $business->email = $request->email;
        $business->password = bcrypt($request->password);  //put condition on confirmation here or frontend?
        $business->save();  
        return response()->json($business, 200);
    }
 
    function sendNotification(Request $request){
        $sender_id = auth()->user()->id;
        $receiver_id = $request->receiver_id;
        $var = Notification::where('sender_id','=',$sender_id)
                           ->where('receiver_id','=',$receiver_id)
                           ->get();
        if($var->isEmpty()){
                $notifcation =  new Notification;
                $notifcation->sender_id = $sender_id;
                $notifcation->receiver_id = $receiver_id;
                $notifcation->body = $request->body;
                $notifcation->created_at = Carbon::now();
                $notifcation->save();        
                $response="OK";
                return response()->json($response, 200);
        } //nothing to return..
        else{
            return response()->json('Alert',200);
        }
    }

    function getNotifications(){
        $receiver_id = auth()->user()->id;
        $response = Notification::where('receiver_id','=',$receiver_id)
                                ->select('sender_id','body')
                                ->get();
        return response()->json($response, 200); 

    }

    function deleteNotification(Request $request){
        $receiver_id = auth()->user()->id;
        $sender_id = $request->sender_id;
        $res = Notification::where('receiver_id','=',$receiver_id)
                            ->where('sender_id','=',$sender_id)
                            ->delete();
    }

    function getToken(Request $request){
        $id = $request->id;
        $response = User::where('id','=',$id)->get();
        
        return response()->json($response[0], 200); 
    }

    function remainingAllowance(){
            $id = auth()->user()->id; 
            $date = Carbon::now();
            $first = Business::where('id',$id)->select('created_at')->get();
            $first_day = $first[0]['created_at'];
            $floor=$first_day->diff($date)->days%7;
            $start = $date->subDays($floor);
            $join = Business::join('exchanges', 'businesses.id', '=', 'exchanges.business_id');
            $business = $join ->where('exchanges.created_at', '>' ,$start)
                                ->where('business_id','=',$id)
                                ->selectRaw('weekly_limit,longitude, latitude ,name,business_id,sum(amount) as sum')
                                ->get();  
            if($business->isEmpty()){
                $business = Business::find($id);
                $business[0]->allowance = $business[0]['weekly_limit'];
                return response()->json($business, 200);
            }  
            else{
                $sum = (int) $business[0]->sum;
                $limit = (int) $business[0]->weekly_limit;
                $business[0]->allowance = $limit-$sum;
                return response()->json($business, 200);
            }
    }

    function dailySums(){
        $id = auth()->user()->id; 
        $date = Carbon::now();
        $first = Business::where('id',$id)->select('created_at')->get();
        $first_day = $first[0]['created_at'];
        $floor=$date->diff($first_day)->days%7;
        $start = $date->subDays($floor);
        $join = Business::join('exchanges', 'businesses.id', '=', 'exchanges.business_id');
        $business = $join ->where('exchanges.created_at', '>' ,$start)
                            ->where('business_id','=',$id)
                            ->groupBy(DB::raw('DATE(exchanges.created_at)'))
                           ->selectRaw('sum(amount) as sum, DATE_FORMAT(exchanges.created_at, "%Y-%m-%d") as date')
                            ->get();  
       return response()->json($business, 200);

    }

    function returnDate() {
        $id = auth()->user()->id; 
        $date = Carbon::now();
        $first = Business::where('id',$id)->select('created_at')->get();
        $first_day = $first[0]['created_at'];
        $floor=$date->diff($first_day)->days%7;
        $next_weekStart = $date->subDays($floor)->addDays(7);
        $timestamp = Date('Y-m-d',strtotime($next_weekStart));
        
        return response()->json($timestamp, 200);

    }

    
}