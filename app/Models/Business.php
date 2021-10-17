<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Business extends Model 
{
    protected $table = "businesses";

    function exchange(){
        return $this->belongsToMany(User::class, 'exchanges');
    }
    
     function locations()
    {
        return $this->hasMany(BusinessLocation::class);
    }

     function category()
    {
        return $this->belongsTo(category::class);
    }

    function scopeNotexceed($query, $date){
         
         return $query->where('weekly_limit','>', $sum);
       
    }
    

}
