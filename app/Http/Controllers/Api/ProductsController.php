<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\Product;
use App\Models\FavProduct;
use Illuminate\Support\Facades\Validator;
use App\Traits\FileUpload;

class ProductsController extends Controller
{
 use FileUpload;
    public function index(){
        return view('admin.barcode');
    }
    public function getAll(Request $request){
        $user_id = $request->user_id ;
        $products =  Product::where('user_id' , $request->service_provider)->get();
        foreach ($products as $product) {
            $favourits = FavProduct::where('product_id',$product->id)->where('user_id',$user_id)->count(); 
            if($favourits != 0){
                $product->is_favourite= 1;
            }else{
                $product->is_favourite= 0;
            }
        }
        
        if($products != null){
            return response()->json([
                "code"=>200,'status' => "sucess",
                "message"=>"all products",
                "data"=>$products
            ]);
        }else{
            return response()->json([
                "code"=>200,'status' => "sucess",
                "message"=>"عفوا لايوجد خدمات قد تم اضافتها بعد",
                "data"=>$products
            ]);
        }  
    
    }
     public function getByCategoryId(Request $request){
        $user_id = $request->user_id ;
        $products =  Product::where('category_id' , $request->category_id)->get();
        foreach ($products as $product) {
            $favourits = FavProduct::where('product_id',$product->id)->where('user_id',$user_id)->count(); 
            if($favourits != 0){
                $product->is_favourite= 1;
            }else{
                $product->is_favourite= 0;
            }
        }
        if($products != null){
            return response()->json([
                "code"=>200,'status' => "sucess",
                "message"=>"all products",
                "data"=>$products
            ]);
        }else{
            return response()->json([
                "code"=>200,'status' => "sucess",
                "message"=>"عفوا لايوجد خدمات قد تم اضافتها بعد",
                "data"=>$products
            ]);
        }
         
     }
    //  public function getAllMeat(){
    //     $products =  SouqProducts::where('shop_id',0)->get();
    //       return response()->json([
    //          "code"=>200,'status' => "sucess",
    //          "message"=>"كل الزبائح",
    //          "data"=>$products
    //     ]);
         
    //  }
     public function getById(Request $request){
         $product = Product::with('user')->find($request->id);
         if($product == null){
            return response()->json([
                "code"=>200,'status' => "error",
                "message"=>"there is no product with this id",
                "data"=>[]
             ]);
         }
            $favourit = FavProduct::where('product_id',$product->id)->where('user_id',$request->user_id)->count(); 
            if($favourit != 0){
                $product->is_favourite= 1;
            }else{
                $product->is_favourite= 0;
            }
        
         if($product){
             return response()->json([
                 "code"=>200,'status' => "sucess",
                 "message"=>"get product by it's id",
                 "data"=>$product
              ]);
         }else{
             return response()->json([
                 "code"=>200,'status' => "error",
                 "message"=>"there is no product with this id",
                 "data"=>[]
              ]);
         }
         
         
     }

     public function searchUserProducts(Request $request){
        
           $products = Product::where('user_id',$request->user_id)->where('title' ,'LIKE','%'.$request->title.'%')->with('user')->get(); 
          
        if($products){
            return response()->json([
                "code"=>200,'status' => "sucess",
                "message"=>"نتائج البحث",
                "data"=>$products
             ]);
        }else{
            return response()->json([
                "code"=>200,'status' => "error",
                "message"=>"عفوا لا يوجد منتج بهذا الاسم",
                "data"=>[]
             ]);
        }
        
        
    }

     public function filter(Request $request){
        $user_id = $request->user_id ;
        $products = Product::where('category_id',$request->category_id)
        ->whereBetween('price',[$request->min_price,$request->max_price])->get();
        foreach ($products as $product) {
            $favourits = FavProduct::where('product_id',$product->id)->where('user_id',$user_id)->count(); 
            if($favourits != 0){
                $product->is_favourite= 1;
            }else{
                $product->is_favourite= 0;
            }
        }
        if($products != null){
            return response()->json([
                "code"=>200,'status' => "sucess",
                "message"=>"all products",
                "data"=>$products
            ]);
        }else{
            return response()->json([
                "code"=>200,'status' => "sucess",
                "message"=>"عفوا لايوجد خدمات قد تم اضافتها بعد",
                "data"=>$products
            ]);
        }
     }
     // get products that added by specific user
    //  public function getAllByShopId($shop_id){
    //      $products = SouqProducts::where("shop_id",$shop_id)->get();
    //      if($products){
    //          return response()->json([
    //              "code"=>200,'status' => "sucess",
    //              "message"=>"get product by souq id",
    //              "data"=>[$products]
    //           ]);
    //      }else{
    //          return response()->json([
    //              "code"=>200,'status' => "error",
    //              "message"=>"there is no products added yet",
    //              "data"=>[]
    //           ]);
    //      }
         
         
    //  }
     
     public function create(Request $request)
     {
        $user = User::find($request->user_id);
      if($user->type_id != 3){
        return response()->json([
            "code"=>200,'status' => "error",
            "message"=>"عفوا انت لا تملك صلاحيه لاضافه خدمات  ",
            "data"=>[]
        ]);
      }
         $rules = [
            'title' => 'required|max:100',
            'category_id' => 'required',
            'image' => 'required|file|image|mimes:jpeg,png,gif,jpg,svg|max:300',
            'user_id' => 'required',
            'price'=> 'required',
            "details" => 'required'
         
         ];

        //  $validation = Validator::make($request->all() ,  $rules);
        //  if ($validation->fails()){
        //      return redirect()->back()->withErrors($validation)->withInputs($request->all());
        //  }else
        //  {
             $product = new Product();
             $product->title    = $request->title;
             $product->price    = $request->price;
             $product->user_id   = $request->user_id; 
             $product->details  = $request->details;
             $product->category_id = $request->category_id;
              if(isset( $request->offer_id)){ $product->offer_id    = $request->offer_id;}         
            
             $product->save();

             if ($request->hasFile('image')) {
                $path = $this->storeFile($request, [
                    'fileUpload' => 'image',
                    'folder' => Product::FILE_FOLDER,
                    'recordId' => $product->id,
                ]);
                $product->Update(['image' => $path]);
             }
            
                 return response()->json([
                     "code"=>200,'status' => "sucess",
                     "message"=>"تم الحفظ بنجاح",
                     "data"=>[$product]
             ]);
      //  }
             
            //      return response()->json([
            //          "code"=>200,'status' => "error",
            //          "message"=>"هناك خطأ",
            //          "data"=>[]
            //  ]);
 
    }
 
 
     public function update(Request $request,$id){
        $user = User::find($request->user_id);
        if($user->type_id != 3){
          return response()->json([
              "code"=>200,'status' => "error",
              "message"=>"عفوا انت لا تملك صلاحيه لتعديل هذه الخدمه ",
              "data"=>[]
            ]);
        }
         $rules = [
            'title' => 'max:100',
            'image' => 'file|image|mimes:jpeg,png,gif,jpg,svg|max:300',
            'price'=> 'numeric',
            "details" => 'string',
            'offer_id' => 'nullable|numeric'
         ];
        //  $validation = Validator::make($request->all() ,  $rules);
        //  if ($validation->fails()){
        //      return redirect()->back()->withErrors($validation)->withInputs($request->all());
        //  }else
        //  {
             $product =  Product::find($id);
             $product->title    = $request->title;
             if(isset($request->price)){  $product->price = $request->price;}
             if(isset($request->details  )){ $product->details  = $request->details; } 
              if(isset( $request->offer_id)){ $product->offer_id    = $request->offer_id;}  
              if(isset($request->category_id)){$product->category_id = $request->category_id;}
             $Result = $product->save();

             if ($request->hasFile('image')) {
                $path = $this->storeFile($request, [
                    'fileUpload' => 'image',
                    'folder' => Product::FILE_FOLDER,
                    'recordId' => $product->id,
                ]);
                $product->Update(['image' => $path]);
             }
            
             
 
             if($Result==1)
             {
                 return response()->json([
                     "code"=>200,'status' => "sucess",
                     "message"=>"تم الحفظ بنجاح",
                     "data"=>[$product]
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
        //  }
     }
 
     public function remove(Request $request){
          $product = Product::find($request->id);
          $Result = $product->delete();
        
          if($Result==1)
          {
                $favourits = FavProduct::where('product_id',$request->id)->get();
                foreach( $favourits  as $favourite){
                    $favourite->delete(); 
                }
            
                return response()->json([
                    "code"=>200,'status' => "sucess",
                    "message"=>"تم الحذف بنجاح",
                    
            ]);
          }
          else
          {
              return response()->json([
                  "code"=>200,'status' => "error",
                  "message"=>"هناك خطأ",
          ]);
         }
     } 
}
