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


class sellerController extends Controller
{   
    public function searchBusinesses(Request $request){
        $name = $request->name."%";
        $date = Carbon::now();
        $getBusinesses = Business::get();
        $businesses_exchanged= array();
        $i=0;
        foreach($getBusinesses as $bus){
            $date = Carbon::now();
            $start = $date->subDays(($bus->created_at->diff($date)->days)%7);
            $sum= Exchange::where([['business_id','=',$bus->id],['created_at', '>' ,$start]])
                            ->sum('amount');
            $businesses_exchanged[$i]['allowance']=$bus->weekly_limit-$sum;
            $businesses_exchanged[$i]['id']=$bus->id;
            $businesses_exchanged[$i]['name']=$bus->name;
            $i++;
        }
        $response = array();
        $exceeded = array();
        $ids = array();
        $i=0;
        $j=0;
        foreach($businesses_exchanged as $business){
            $allowance = $business['allowance'];
            if( $allowance > 0 && str_contains($request->name,$business['name'])){
                $ids[]= $business['id'];
                $response[$i]= $business;
                $i++;
            }
            else {
                $exceeded[$j]['name'] = $business['name']; 
            }
        }  
        $businesses = Business::distinct()
                        ->select('id','weekly_limit','name','longitude', 'latitude')
                        ->where('name','LIKE',$name)
                         ->whereNotIn('name',$exceeded)
                         ->whereNotIn('id',$ids)
                         ->get();               
        foreach($businesses as $business){
            $response[$i]=$business;
            $response[$i]['allowance'] = $business->weekly_limit;   
            $i++; 
        }        
        return response()->json($businesses, 200);       
    }


    function getBuyers(){        
        $date = Carbon::now();
        $getBusinesses = Business::get();
        $businesses_exchanged= array();
        $i=0;
        foreach($getBusinesses as $bus){
            $date = Carbon::now();
            $start = $date->subDays(($bus->created_at->diff($date)->days)%7);
            $sum= Exchange::where([['business_id','=',$bus->id],['created_at', '>' ,$start]])
                            ->sum('amount');
            $businesses_exchanged[$i]['allowance']=$bus->weekly_limit-$sum;
            $businesses_exchanged[$i]['id']=$bus->id;
            $businesses_exchanged[$i]['name']=$bus->name;
            $i++;
        }
        $response = array();
        $exceeded = array();
        $ids = array();
        $i=0;
        $j=0;
        foreach($businesses_exchanged as $business){
            $allowance = $business['allowance'];
            if( $allowance > 0){
                $ids[]= $business['id'];
                $response[$i]= $business;
                $i++;
            }
            else {
                $exceeded[$j]['name'] = $business['name']; 
            }
        }  
        $businesses = Business::distinct()
                        ->select('id','weekly_limit','name','longitude', 'latitude')
                         ->whereNotIn('name',$exceeded)
                         ->whereNotIn('id',$ids)
                         ->get();               
        foreach($businesses as $business){
            $response[$i]=$business;
            $response[$i]['allowance'] = $business->weekly_limit;   
            $i++; 
        }        
        return response()->json($response, 200);   
    }

    function filter(Request $request){
        $date = Carbon::now();
        $getBusinesses = Business::get();
        $businesses_exchanged= array();
        $i=0;
        foreach($getBusinesses as $bus){
            $date = Carbon::now();
            $start = $date->subDays(($bus->created_at->diff($date)->days)%7);
            $sum= Exchange::where([['business_id','=',$bus->id],['created_at', '>' ,$start]])
                            ->sum('amount');
            $businesses_exchanged[$i]['allowance']=$bus->weekly_limit-$sum;
            $businesses_exchanged[$i]['id']=$bus->id;
            $businesses_exchanged[$i]['name']=$bus->name;
            $i++;
        }
        //return response()->json($businesses_exchanged,200);

        $amount = $request->amount;    
        $response = array();
        $exceeded = array();
        $ids = array();
        $i=0;
        $j=0;
        foreach($businesses_exchanged as $business){
            $allowance = $business['allowance'];
            if( $allowance > $amount){
                $ids[]= $business['id'];
                $response[$i]= $business;
                $i++;
            }
            else {  
                $exceeded[$j]['name'] = $business['name']; 
                $j++;
            }
        }
        $businesses = Business::select('id','weekly_limit','name','longitude', 'latitude')
                                ->whereNotIn('name',$exceeded)
                                ->whereNotIn('id',$ids)
                                ->get(); 

        foreach($businesses as $business){
            $limit =$business->weekly_limit; 
            if($limit > $amount){
                $response[$i]=$business;
                $response[$i]['allowance'] = $business->weekly_limit;
                $i++;
            }
        }
    return response()->json($response, 200);  

    }

    function exchange(Request  $request){
        $exchange =  new Exchange;
        $user_id = auth()->user()->id;
        $exchange->user_id = $user_id;
        $exchange->business_id = $request->business_id;
        $exchange->amount = $request->amount;
        $exchange->created_at = Carbon::now();
        $exchange->save();
        
        $response="OK";
        return response()->json($response, 200); //nothing to return..
    }

     public function getProfile(Request $request){
        $business_id =  $request->id;
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

    function editApi(Request $request){  //for business not user.
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
    
    function getAvgRates(){
        $date = Carbon::now();
        $start = $date->subDays(5);;
        $timestamp = strtotime($start);
        $days=  array();
        for($i=0; $i<6 ; $i++){
            $days[$i]= date('D', $timestamp);
            $day = $start->addDay();
            $timestamp= strtotime($day);
        }
        $start = $date->subDays(5);
        $avgs = Rate::where ('created_at','>',$start)
                    ->groupBy("day")
                    ->selectRaw('day, cast(avg(rate) as decimal(10,3)) ')
                    ->orderBy('day', 'asc')
                    ->get();              

        $response["days"]=$days;
        $response["avgs"]=$avgs;
        return response()->json($response, 200);

    }

    function scrap(){
        $results  = array();
        $client =  new Client();  
        $url ='https://www.omt.com.lb/en';
        $page = $client->request('GET', $url);
       $exchange = $page->filter('.rate')->text() ;
       $rate = explode(' ',$exchange);
       $a=str_replace(',','',$rate[3]);
       $value = new Rate;
       $value->rate = (int)($a);     
       $value->day = Carbon::now();
       $value->save();
       return response()->json((int)($a),200);
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

    function getSellerNotifications(){
        $receiver_id = auth()->user()->id;
        $join = Business::join('notifications', 'businesses.id', '=', 'notifications.sender_id');
        $response = $join->where('receiver_id','=',$receiver_id)
                            ->select('name','body','picture_url','sender_id')
                            ->orderBy('body','DESC')
                            ->get();

        $join = Business::join('notifications', 'businesses.id', '=', 'notifications.receiver_id');

        $rest= $join ->where('sender_id', '=', $receiver_id)
                                      ->where('body', 'LIKE', '%pinged%')
                                      ->select('name','body','picture_url','receiver_id')
                                      ->get();
        $all_notifications["responded"] = $response;
        $all_notifications["pending"] = $rest;
        return response()->json($all_notifications, 200); 

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

    


}