<?php

namespace App\Services;

use App\Models\Order;
use App\User;
use App\Models\OrderItem;
use App\Models\OrderOffer;
use App\Services\NotificationService;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
class OrderService
{

    use FileUpload;
    private $notificationServ;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationServ = $notificationService;
    }

    public function store( $request )
    {
        $order = new Order();
        $user = User::find($request['user_id']);
        // $user = User::find(1);

        $user_static=['name'=>$user->name,'rate'=>$user->rate,'rate_count'=>$user->rate_count,'image'=>$user->imagePath()];
        $order->user_static = $user_static;
        $order->user_id = $request['user_id'];
        if (isset($request['comment'])) {$order->comment = $request['comment'];} 
        if (isset($request['source_lat'])) {$order->source_lat = $request['source_lat'];}
        if (isset($request['source_lng'])) {$order->source_lng = $request['source_lng'];}
        if (isset($request['discount'])) {$order->discount = $request['discount'];}
        if (isset($request['payment_type'])) {$order->payment_type = $request['payment_type'];}

        // if (isset($request['shop_name'])) {$order->shop_name = $request['shop_name'];}
        if (isset($request['package_type'])) {$order->package_type = $request['package_type'];}
        // if (isset($request['addition_service'])) {
        //     $order->addition_service = $request['addition_service'];
        //     // $order->addition_service_cost =\App\Models\Setting::find(1)->addition_service_cost;
        
        // }
       
        // if (isset($request['total'])) {
        //     $commission= $request['total'] * (\App\Models\Setting::find(1)->commission_percent / 100);
        //     $order->total = $request['total'] +  $commission;
        // }
        $order->commission_status = 1;
        $order->addition_service = 1;
        $order->destination_lng = $request['destination_lng'];
        $order->destination_lat = $request['destination_lat'];
        // $order->type = $request['type'];
        $order->status = 1;
        $order->save();

        if (!$order) {
            return false;
        }

        $order->code = $order->id;
        //$order->access_user_id = $order->user_id;
        $order->save();
        $itemsPrice = 0;
        foreach ($request['items'] as $key=>$itemData) {
            $item = new OrderItem();
            $item->title = $itemData['title'];
            $item->order_id = $order->id;
            $item->quantity = $itemData['quantity'];
            if (isset( $itemData['price'] )) {
                $itemsPrice = $itemsPrice + ($itemData['price'] *   $itemData['quantity'] )  ;
            }
            $path = null;
            if (isset($itemData['image'])) {       
                if(isset($_FILES['items']['name'][$key]['image']) ){
                    $path = $this->storeFile(false, [
                        'fileUpload' => $itemData['image'],
                        'folder' => OrderItem::FILE_FOLDER,
                        'recordId' => $item->id . '_image',
                    ]);
                }else{
                $path = $itemData['image'];
                    
                }

                // if ($path === false) {
                //     $path = $itemData['image'];
                // }
            }
            $item->image = $path;
            $item->save();
        }
              
                //save service and it's provider at order table
        $order->price = $itemsPrice ;
        $order->commission= $order->price * (\App\Models\Setting::find(1)->commission_percent / 100);
        $ordershipping = $order->price  +  $order->commission;
        $order->addition_service_cost = $ordershipping  * (\App\Models\Setting::find(1)->addition_service_cost / 100);
        $order->total =  $ordershipping + $order->addition_service_cost - $order->discount;
        $shipper=User::find($request['items'][0]['user_id']);
        $order->shipper_id = $shipper->id;
        $shipper_static=['name'=>$shipper->name,'rate'=>$shipper->rate,'rate_count'=>$shipper->rate_count,'image'=>$shipper->imagePath()];
        $order->shipper_static = $shipper_static;
          $order->save();
            
        $this->notificationServ->notifySouqOrder(
            ['fcm', 'db'],
            ['order_id' => $order->id, 'user_id' => $shipper->id]
        ); 
        $order = Order::with('items')->with('offer')->with('shipper_data')->with('user_data')->findOrFail($order->id);

        return $order;
    }
    public function storeSecondType( $request )
    {
        $order = new Order();
        $user = User::find($request['user_id']);
        // $user = User::find(1);

        $user_static=['name'=>$user->name,'rate'=>$user->rate,'rate_count'=>$user->rate_count,'image'=>$user->imagePath()];
        $order->user_static = $user_static;
        $order->user_id = $request['user_id'];
        if (isset($request['comment'])) {$order->comment = $request['comment'];} 
        if (isset($request['type'])) {$order->type = $request['type'];} 
        if (isset($request['discount'])) {$order->discount = $request['discount'];}
        if (isset($request['package_type'])) {$order->package_type = $request['package_type'];}
        if (isset($request['destination_lat'])) {$order->destination_lat = $request['destination_lat'];}
        if (isset($request['destination_lng'])) {$order->destination_lng = $request['destination_lng'];}
        if (isset($request['details'])) {$order->details = $request['details'];}
        $order->commission_status = 1;

    
        $order->status = 1;
        $order->save();

        if (!$order) {
            return false;
        }

        $order->code = $order->id;
        // $order->access_user_id = $order->user_id;
        $order->save();
        $users = User::where('type_id',3)->where('category','LIKE','%'.$request->type.'%')->where('approved',1)->get();
        $this->notificationServ->notifyOrder(
            ['fcm', 'db'],
            ['order_id' =>  $order->id, 'users' => $users ]
        );
               
        $order = Order::with('items')->with('offer')->with('shipper_data')->with('user_data')->findOrFail($order->id);

        return $order;
    }
    public function updateStatus()
    {

    }

    public function submitOffer($request)
    {

        $offer =  OrderOffer::create($request);
        if (!$offer) {
            return false;
        }
        return $offer;

    }

    

}
