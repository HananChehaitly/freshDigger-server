<?php 

namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Seller;
use App\Models\Business;
use App\Models\Exchange;
use App\Models\Category;
use App\Models\User;
use Validator;
use Carbon\Carbon;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;


class SellerController extends Controller
{ 

    public function test(){
        $user = auth()->user();        
        return response()->json($user, 200);  
    }

}