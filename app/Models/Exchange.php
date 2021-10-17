<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Exchange extends Model 
{
    use HasFactory;
    public function scopeCompute($query, $date, $id){
        $start = $date->subDays(2);   //Should change this to 6 later. 
        //  return  $query->where('created_at', '>' ,$start)
        //                  ->groupBy('business_id') 
        //                  ->selectRaw('sum(amount) as sum, business_id')
        //                  ->pluck('sum','business_id'); 
         //   return response()->json($query, 200);
        
    }

}
