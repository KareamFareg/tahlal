<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
class rate extends Model
{
    protected $table='rates';
    protected $fillable = [
   'user_id','order_id','rate','comment'
    ];
public function order(){
    
    return $this->belongsToMany(Order::class);
   
}


}
