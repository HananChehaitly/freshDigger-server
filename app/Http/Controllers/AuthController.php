<?php

namespace App\Http\Controllers;
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


class AuthController extends Controller
{   use HasFactory;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422); 
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->user());
    }

     public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:3',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
		
        //Register depending on user_type in $request.
        
        if( $request->user_type_id==2 && auth()->user()->id==1 ){
            $user = new User;
                $user->email = $request->email;
                $user->password = bcrypt($request->password);
                $user->user_type_id=2;
                $user->expoToken = $request->expoToken;
                $user->save();
            $business = new Business;
			    $business->id = $user->id;
                $business->name = $request->name;
                $business->weekly_limit = $request->weekly_limit;
                $business->phone_number = $request-> phone_number;
                $business->latitude = $request ->latitude;
                $business->longitude = $request ->longitude;
                // search for category id from the request->category to set the value.
                $business->save();
            return response()->json([
            'message' => 'Business successfully registered',
            'user' => $business
            ], 201);
        }
    else{
        $user = new User;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->user_type_id =3;
        $user->expoToken = $request->expoToken;
        $user->save();
        return response()->json([
        'message' => 'User successfully registered',
        'user' => $user
        ], 201);
}
    }
    
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    public function searchBusinesses(Request $request){
        $name = "%".($request->name)."%";
        $date = Carbon::now();
        $start = $date->subDays(2);   //Should change this to 6 later. 
        $join = Business::join('exchanges', 'businesses.id', '=', 'exchanges.business_id');
        $businesses = $join->where('exchanges.created_at', '>' ,$start)
                                           ->groupBy('business_id')
                                           ->selectRaw('weekly_limit,longitude, latitude ,name,business_id,sum(amount) as sum')
                                           ->get();              
        $response = array();
        $i =0;
        foreach($businesses as $business){
             $sum = (int) $business->sum;
             $limit = (int) $business->weekly_limit;
             if( $sum > $limit){
                 $response[$i]['name'] = $business->name;
                 $i++;
             }
        }
       $businesses = Business::distinct()
                        ->select('id','name','longitude', 'latitude')
                         ->where('name','LIKE' ,"$name")
                         ->whereNotIn('name',$response)
                         ->get();
       return response()->json($businesses, 200);
    }
       
    function getBusinesses(Request $request){
        $date = Carbon::now();
        $start = $date->subDays(2);   //Should change this to 6 later. 
        $join = Business::join('exchanges', 'businesses.id', '=', 'exchanges.business_id');
        $businesses = $join->where('exchanges.created_at', '>' ,$start)
                                           ->groupBy('business_id')
                                           ->selectRaw('weekly_limit,longitude, latitude ,name,business_id,sum(amount) as sum')
                                           ->get();              
        $response = array();
        $i =0;
        foreach($businesses as $business){
             $sum = (int) $business->sum;
             $limit = (int) $business->weekly_limit;
             if( $sum > $limit){
                 $response[$i]['name'] = $business->name;
                 $i++;
             }
        }
       $businesses = Business::distinct()
                        ->select('id','name','longitude', 'latitude')
                         ->whereNotIn('name',$response)
                         ->get();
       return response()->json($businesses, 200);   
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
    
        $response['bio']= $business->bio;
         $email = User::find($business_id)->email;
         $response['email']= $email;
         $response['expoToken']= User::find($business_id)->expoToken;
        return response()->json($response, 200);
    }

   
    function searchByCat(Request $request){ //need to be changed to not show those which exceeded.
        $name =  $request->name."%";
        $cat_id =  Category::where('name','LIKE',$name)->first()->id;
        $businesses = Business::select('id','name', 'email', 'picture_url')->where('category_id','=',$cat_id)->get();
        return response()->json($businesses, 200);
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

    function getChatsApi(Request $request){
        $user_id = auth()->user()->id;
        $businesses  =  Exchange::distinct()->select('business_id')->where('user_id','=',$user_id)->get();
        $response=[];
        foreach($businesses as $business){
            $business_id =  $business->business_id;
            $bus =  Business::select('name','picture_url')->where('id','=',$business_id)->first();
            $response[]=$bus;
        }
        return response()->json($response, 200);
    }
    
    function getAvgRates(){
        $date = Carbon::now();
        $start = $date->subDays(5);
        $timestamp = strtotime($start);
        $days=  array();
        for($i=0; $i<6 ; $i++){
            $days[$i]= date('D', $timestamp);
            $day = $start->addDay();
            $timestamp= strtotime($day);
        }
        $avgs = Rate::groupBy("day")
                    ->selectRaw('day, cast(avg(rate) as decimal(10,3)) ')
                    ->orderBy('day', 'desc')
                    ->take(6)
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
        $notifcation =  new Notification;
        $sender_id = auth()->user()->id;
        $notifcation->sender_id = $sender_id;
        $notifcation->receiver_id = $request->receiver_id;
        $notifcation->body = $request->body;
        $notifcation->created_at = Carbon::now();
        $notifcation->save();
        
        $response="OK";
        return response()->json($response, 200); //nothing to return..
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
}