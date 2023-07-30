<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\Product;
use App\Models\FavProduct;
use Illuminate\Support\Facades\Validator;
use App\Traits\FileUpload;

class FavProductsController extends Controller
{
  
    public function getAll($id){
        $products =  FavProduct::where('user_id' , $id)->with('product')->get();
        if($products != null){
            return response()->json([
                "code"=>200,'status' => "sucess",
                "message"=>"كل المنتجات المفضله لديك",
                "data"=>$products
            ]);
        }else{
            return response()->json([
                "code"=>200,'status' => "sucess",
                "message"=>" عفوا لايوجد منتجات قد تم اضافتها بعد" ,
                "data"=>$products
            ]);
        }
    
    }

     public function create(Request $request)
     {
  
         $rules = [
            'product_id' => 'required',
            'user_id' => 'required',   
         ];

         $validation = Validator::make($request->all() ,  $rules);
         if ($validation->fails()){
             return redirect()->back()->withErrors($validation)->withInputs($request->all());
         }else
         {
                $product = new FavProduct();
                $product->user_id   = $request->user_id; 
                $product->product_id  = $request->product_id;
                
                $product->save();
            
                    return response()->json([
                        "code"=>200,'status' => "sucess",
                        "message"=>"تم الحفظ بنجاح",
                        "data"=>[$product]
                ]);
            
            }     
                 return response()->json([
                     "code"=>200,'status' => "error",
                     "message"=>"هناك خطأ",
                     "data"=>[]
             ]);
           
    }
 
     public function remove(Request $request){
        $Result = FavProduct::where('user_id',$request->user_id)->where('product_id',$request->product_id)->delete();
        //   $Result = $product->delete();
        
          if($Result==1)
          {
              return response()->json([
                  "code"=>200,'status' => "sucess",
                  "message"=>"تم الحذف المنتج من قائمه المفضله بنجاح",
                  "data"=>[]
          ]);
          }
          else
          {
              return response()->json([
                  "code"=>200,'status' => "error",
                  "message"=>"هناك خطأ",
                  "data"=>[]
          ]);
         }
     } 
}
