<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Business;
use App\Models\Exchange;
use App\Models\Category;
use Validator;
use Carbon\Carbon;


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
		// $time = strtotime($birthday);
    	// $newformat = date('Y-m-d',$time);

        $user = new User;
			$user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
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

    function searchBusiness(Request $request){
        $name = $request->name."%";
        $date = Carbon::now();
        $start = $date->subDays(2);   //Should change this to 6 later. 
        $bus_amounts = DB::table('businesses')
                         ->join('exchanges', 'businesses.id', '=', 'exchanges.business_id');
        $businesses = $bus_amounts->where('name','LIKE' ,"$name")
                                  ->where('exchanges.created_at', '>' ,$start)
                                  ->groupBy('business_id')
                                  ->selectRaw('weekly_limit,picture_url ,name,business_id,sum(amount) as sum')
                               //   ->where('weekly_limit','>','sum')
                                  ->get();                       
        $response = array();
        foreach($businesses as $business){
             $i =0;
             $sum = (int) $business->sum;
             $limit = (int) $business->weekly_limit;
             if( $sum < $limit){
                 $response[$i] = $business;
                 $i++;
             }
       }
    return response()->json($response, 200);
    }

       


    function exchange(Request  $request){
        $exchange =  new Exchange;
        $user_id = auth()->user()->id;
        $exchange->business_id = $request->business_id;
        $exchange->amount = $request->amount;
        $exchange->created_at = Carbon::now();
        $exchange->save();
        
        $response="OK";
        return response()->json($response, 200); //nothing to return..
    }

    function getProfile(Request $request){
        $business_id =  $request->id;
        $business = Business::find($business_id);
        $response['name'] =  $business->name;
        $response['email'] =  $business->email;
        $response['picture_url'] =  $business->picture_url;
        $cat_id =  $business->category_id;
        $category = Category::find($cat_id);
        $response['category'] = $category->name;
        return response()->json($response, 200);
    }
    

    function searchByCat(Request $request){
        $name =  $request->name."%";
        $cat_id =  Category::where('name','LIKE',$name)->first()->id;
        $businesses = Business::select('name', 'email', 'picture_url')->where('category_id','=',$cat_id)->get();
        return response()->json($businesses, 200);
    }

    function addPicture(Request $request){
        $image = $request->image;  // your base64 encoded
        $imageName = "str_random(".rand(10,1000).")".'.'.'jpeg';
        $path=public_path();
        \File::put($path. '/image/' . $imageName, base64_decode($image));
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $user->p_path = '/image/'.$imageName;
        $user->save();
        return response()->json($user, 200);
    }

    function editApi(Request $request){  //for business not user.
        $business_id = auth()->user()->id;     //how to make auth on businesses???? just say auth->business???
        $business = Business::find($business_id);
        $business->name = $request->name;
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
    function rateChart(){
        //for display of last week's changes in rate.
        // rates should be in mysql or firebase?
     
    }

    function getMessages(){
        //firebase
     
    }

    function getNotifications(){
        //firebase
     
    }

    function getMap(){
        //firebase
     
    }

    

}