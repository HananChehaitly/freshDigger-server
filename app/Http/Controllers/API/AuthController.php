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

    
}