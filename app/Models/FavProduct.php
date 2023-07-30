<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavProduct extends Model
{
    protected $table = 'fav_products';
    protected $fillable = [
        'user_id', 'product_id',
    ];
    protected $hidden = [
        'updated_at',
    ];


    public function product()
    {
        return $this->belongsto('App\Models\Product','product_id') ;
    }
}
