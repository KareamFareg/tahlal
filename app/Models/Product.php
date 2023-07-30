<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    const FILE_FOLDER = 'products';
    const PAGE = 'products';

    protected $table = 'products';
    protected $fillable = [
        'image', 'title', 'category_id','offer_id','price','user_id','details'
    ];
    protected $hidden = [
        'updated_at','created_at'
    ];
    public function user()
    {
        return $this->belongsto('App\User','user_id') ;
    }
    
}
