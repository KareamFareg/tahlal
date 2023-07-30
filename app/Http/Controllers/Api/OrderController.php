<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderOfferRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\UserResource;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderOffer;
use App\Models\Place;
use App\Models\Rate;
use App\Models\Shop;
use App\Models\Transaction;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Traits\ApiResponse;
use App\Traits\FileUpload;
use App\Traits\GeneralTrait;
use App\User;
use Illuminate\Http\Request;
use App\Models\OrderItem;

class OrderController extends Controller
{
    private $notificationServ;

    use FileUpload, ApiResponse, GeneralTrait;
    private $order;


    public function __construct(Orderservice $orderService, NotificationService $notificationService)
    {
        $this->order = $orderService;
        $this->notificationServ = $notificationService;

    }
   
    //Create new Order throw [ Service $orderService ] 
    
    public function submit(Request $request)
    { 
    
        //return $request->validated();
        if($request->package_type == 1){
            $order = $this->order->store($request);
        }elseif($request->package_type == 2){
            $order = $this->order->storeSecondType($request);
        }
      
        
        if (!$order) {     
            $response = [
            'errors' => ['Order' => trans('messages.added_faild')]];
           return $this->responseFaild($response);
        }
        
        // send success
        $response = [
            'message' => ['sucess' => [trans('messages.added')]],
            'data' => $order->only('id', 'user_id', 'code', 'source_lat', 'source_lng', 'destination_lng', 'destination_lat', 'discount', 'type', 'payment_type', 'status', 'created_at', 'shipper_id', 'items'),

        ];
        
        return $this->responseSuccess($response);
    }

//Re order an order again  by using it's id 
public function acceptOrderByDriver(Request $request){
    $order = Order::with('items')->findOrFail($request->order_id);
    $user_sender =User::find($order->shipper_id);
    $order->status = 2;
    $order->save();
    $this->notificationServ->notifyAcceptOrder(
        ['fcm', 'db'],
        ['order_id' => $request->order_id]
    ); 
    // send success
    $response = [
        'message' => ['sucess' => [__('messages.driver_accept_order', ['user_name' => $user_sender->name,'order_code' => $order->code])]],
        'data' => $order,
    ];
   
    return $this->responseSuccess($response);

}
    public function reorder(Request $request){
      
               
     //return old order data throw order_id
        $this->validate($request, [
            'order_id' => 'required|numeric|exists:orders,id',
        ]);

        $oldOrder = Order::with('items')->findOrFail($request->order_id);
        
        $oldOrder->Update(['status' => 5, 'canceld_by' => 1, 'note' =>'', 'cancel_date' => \Carbon\Carbon::now()]);

        $request=$oldOrder->toArray();
       // dd($request);
        $order = new Order();


        $user=User::find($request['user_id']);  // return Driver data from the last order 
        $user_static=['name'=>$user->name,'rate'=>$user->rate,'rate_count'=>$user->rate_count,'image'=>$user->imagePath()];
        $order->user_static = $user_static;

        $order->user_id = $request['user_id'];
        if (isset($request['comment'])) {$order->comment = $request['comment'];} 
        if (isset($request['source_lat'])) {$order->source_lat = $request['source_lat'];}
        if (isset($request['source_lng'])) {$order->source_lng = $request['source_lng'];}
        if (isset($request['discount'])) {$order->discount = $request['discount'];}
        if (isset($request['shop_name'])) {$order->shop_name = $request['shop_name'];}
        if (isset($request['package_type'])) {$order->package_type = $request['package_type'];}
        if (isset($request['addition_service'])) {
            $order->addition_service = $request['addition_service'];
            $order->addition_service_cost =\App\Models\Setting::find(1)->addition_service_cost;
        
        }
        
        $order->destination_lng = $request['destination_lng'];
        $order->destination_lat = $request['destination_lat'];
        $order->type = $request['type'];
        $order->status = 1;
        $order->save();
        if($request->type == 41){
            $this->notificationServ->notifySouqOrder(
                ['fcm', 'db'],
                ['order_id' => $request->id, 'user_id' => 1]
            );
        }
         if(isset($request->shop_id)){
                $shop = Shop::find($request->shop_id);
                $this->notificationServ->notifySouqOrder(
                    ['fcm', 'db'],
                    ['order_id' => $request->id, 'user_id' => $shop->user_id]
                ); 
         }
        
        $order->code = $order->id + 1000;
        //$order->access_user_id = $order->user_id;
        $RESULT = $order->save();
        if (!$RESULT) {
            $response = [
                'errors' => ['Order' => trans('messages.added_faild')]
                ];
            return $this->responseFaild($response);
        }

        foreach ($request['items'] as $key=>$itemData) {
            $item = new OrderItem();
            $item->title = $itemData['title'];
            $item->order_id = $order->id;
            $item->quantity = $itemData['quantity'];
            $item->save();

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
 
            }

            $item->image = $path;

            $item->save();
        }
         
        // send success
        $response = [
            'message' => ['sucess' => [trans('messages.added')]],
            'data' => $order->only('id', 'user_id', 'code', 'source_lat', 'source_lng', 'destination_lng', 'destination_lat', 'discount', 'type', 'payment_type', 'status', 'created_at', 'shipper_id', 'items'),

        ];
      
        return $this->responseSuccess($response);

    }

//Cancel order from  database
    public function cancel(Request $request)
    {
       
        $id = $request->id;
        $order = Order::findOrFail($id);

        if ($request->type == 1) {
            if ($order->status == 1 || $order->status == 2) {
                if ($order->Update(['status' => 5, 'canceld_by' => $request->canceld_by, 'note' => $request->note, 'cancel_date' => \Carbon\Carbon::now()]) == false) {
                    
                    $response = [
                        'errors' => ['Order' => trans('messages.updated_faild')]];
                    return $this->responseFaild($response);
                }

            } else {
                $response = [
                    'errors' => ['Order' => trans('messages.updated_faild')]];
                return $this->responseFaild($response);
            }

            // send success
            $response = [
          
                'message' => ['sucess' => [trans('messages.updated')]],
                'data' => $order->only('id', 'user_id', 'code', 'source_lat', 'source_lng', 'destination_lng', 'destination_lat', 'discount', 'type', 'status', 'created_at', 'shipper_id', 'items'),
            ];

            $this->notificationServ->notifyCancelOrder(
                ['fcm', 'db'],
                ['order_id' => $request->id, 'canceld_by' => $request->canceld_by]
            );
            return $this->responseSuccess($response);

        } else {
            if ($order->Update(['status' => 5, 'canceld_by' => $request->canceld_by, 'note' => $request->note, 'cancel_date' => \Carbon\Carbon::now()]) == false) {
                $response = ['errors' => ['Order' => trans('messages.updated_faild')]];
                return $this->responseFaild($response);
            }

            $response = [
           
                'message' => ['sucess' => [trans('messages.added')]],
                'data' => $order->only('id', 'user_id', 'code', 'source_lat', 'source_lng', 'destination_lng', 'destination_lat', 'discount', 'type', 'status', 'created_at', 'shipper_id', 'items'),
            ];

            $this->notificationServ->notifyCancelOrder(
                ['fcm', 'db'],
                ['order_id' => $request->id, 'canceld_by' => $request->canceld_by]
            );

            return $this->responseSuccess($response);

        }

    }
    public function getlatestCreatedOrders(){
         $orders = Order::with('items')->with('user_data')->orderBy('created_at', 'desc')->where('status',2)->get();
         $data = $orders->toArray();
         $response = [
            'code'=>200,
            'msg' =>"كل الطلبات التي انشاؤها مؤخرا",
            'data' => $data,
        ];
        return $this->responseSuccess($response);

    }
    public function filterOrdersAsType(Request $request){
            $orders = Order::where('type' ,$request->type)->get();
            $data = $orders->toArray();
            
            $response = [
                'code'=>200,
                'msg' =>"طلبات ",
                'data' => $data,
            ];
       return $this->responseSuccess($response);
   }
//Return order and it's data like [shipping data , and shipper and offer] from Database 
public function details($id,$user_id=null)
{
    
    $order = Order::with('items')->with('shipper_data')->with('user_data')->findOrFail($id);
    
    
    $order->toArray();
    $place = Place::where('user_id', $order['user_data']['id'])->where('lat', $order['destination_lat'])->where('lng', $order['destination_lng'])->first();
    if ($place) {
        $place_name = $place->name;
    } else {
        $place_name = null;
    }

    $data['id'] = $order['id'];
    $data['user_data'] = ($order['user_data'] == null) ? null : new UserResource($order['user_data']);
    $data['shipper_data'] = ($order['shipper_data'] == null) ? null : new UserResource($order['shipper_data']);
    $data['code'] = $order['code'];
    $data['source_lat'] = $order['source_lat'];
    $data['source_lng'] = $order['source_lng'];
    $data['destination_lat'] = $order['destination_lat'];
    $data['destination_lng'] = $order['destination_lng'];
    $data['type'] = $order['type'];
    $data['type_name'] = $order->type_title();
    $data['status'] = $order['status'];
    $data['comment'] = $order['comment'];
    $data['note'] = $order['note'];
    $data['invoice'] = ($order['invoice'] == null) ? null : asset('storage/app/' . $order['invoice']);
    $data['created_at'] = $order['created_at'];
    $data['payment_type'] = $order['payment_type'];
    $data['destination_addres_name'] = $place_name;
    $data['items'] = $order['items'];
    $data['reted_by_client'] = Rate::where(['order_id' => $order->id, 'user_id' => $order->shipper_id])->first() ? 1 : 0;
    $data['reted_by_shipper'] = Rate::where(['order_id' => $order->id, 'user_id' => $order->user_id])->first() ? 1 : 0;
    $data['price'] = $order['price'];
    $data['deserve_more'] = $order['deserve_more'];
    $data['commission'] =  number_format($order['commission'] , 2, '.', '');
    // $data['addition_service'] = $order['addition_service'];
    $data['addition_service_cost'] = $order['addition_service_cost'];
    $data['discount'] = $order['discount'];
    $data['total'] = $order['total'];
    // if($order['payment_status'] == 1){
    //     $data['total'] = $order['total'];
    // }else{
    //     $data['total'] = ceil($data['price'] +  $data['commission'] + $data['addition_service_value'] - $order->discount);
    // }
   
    $data['package_type'] = $order['package_type'];
    $data['user_static'] = $order['user_static'];
    $data['shipper_static'] = $order['shipper_static'];
    
   
    $data['payment_status'] = $order['payment_status'];
    $data['details'] = $order['details'];
    $data['created_at'] = $order['created_at'];
    $data['updated_at'] = $order['updated_at'];
    $response = [
        
        'data' => $data,
    ];
    return $this->responseSuccess($response);
}
public function submitOffer(Request $request)
{
    if(OrderOffer::where(['order_id' => $request->order_id, 'user_id' => $request->user_id])->count() > 0){
        $response = [
            'message' => ['sucess' => 'عفوا تم تقديم عرض لهذا الطلب من قبل'],
        ];
        return $this->responseSuccess($response);
    }
    $orderOffer = new OrderOffer();
    $orderOffer->order_id = $request->order_id; 
    $orderOffer->user_id = $request->user_id; 
    $orderOffer->lat = $request->lat; 
    $orderOffer->lng = $request->lng; 
    $orderOffer->shipping = $request->shipping ; 
    $result  =$orderOffer->save();
    // $offer = $this->order->submitOffer($request);
    if (!$result) {
        $response = ['errors' => ['Offer' => trans('messages.added_faild')]];
        return $this->responseFaild($response);
    }

    $this->notificationServ->notifySubmitOffer(
        ['fcm', 'db'],
        ['order_id' => $request->order_id, 'user_id' => $request->user_id]
    );

    // send success
    $response = [
        'message' => ['sucess' => [trans('messages.added')]],
        'data' => $orderOffer,

    ];
    return $this->responseSuccess($response);
    
}
// CLient accept the offer from Driver [adding Driver to order] 
public function acceptOffer(Request $request)
{
   
$this->validate($request, [
        'offer_id' => 'required|numeric|exists:order_offers,id',
        'order_id' => 'required|numeric|exists:orders,id',
    ]);

    $offer = OrderOffer::findOrFail($request->offer_id);
    $user=User::find($offer->user_id);
    $order=Order::findOrFail($request->order_id);

    $offer->update(['status' => 1]);
    $commission =$offer->shipping * \App\Models\Setting::find(1)->commission_percent;

    OrderOffer::where('order_id', $request->order_id)->where('id', '!=', $request->offer_id)->update(['status' => 2]);
    Order::findOrFail($request->order_id)->update(['status' => 2, 'commission' => $commission, 'shipper_id' => $offer->user_id, 'accept_date' => \Carbon\Carbon::now(), 'price' => $offer->shipping]);
    $order->source_lat = $offer->lat;
    $order->source_lng = $offer->lng;
    $order->price = $offer->shipping;
    $order->commission= $offer->shipping * (\App\Models\Setting::find(1)->commission_percent / 100);
        $ordershipping = $offer->shipping  +  $order->commission;
        $order->addition_service_cost = $ordershipping  * (\App\Models\Setting::find(1)->addition_service_cost / 100);
        $order->total =  $ordershipping + $order->addition_service_cost - $order->discount;

    $shipper_static=['name'=>$user->name,'rate'=>$user->rate,'rate_count'=>$user->rate_count,'image'=>$user->imagePath()];
    $order->shipper_static = $shipper_static;
    $order->save();
    $this->notificationServ->notifyOrderAccepted(
        ['fcm', 'db'],
        ['order_id' => $request->order_id, 'offer_id' => $request->offer_id ]
    );
    // send success
    $response = [
        'message' => ['sucess' => [trans('messages.added')]],
        'data'=> [$order]
    ];
    
     return $this->responseSuccess($response);

}
//Delete Driver Offer that has been sent to Client
public function delete_offer(Request $request)
{
    $this->validate($request, [
        'user_id' => 'required|numeric|exists:users,id',
        'order_id' => 'required|exists:orders,id',

    ]);
    $offer = OrderOffer::where(['order_id' => $request->order_id, 'user_id' => $request->user_id]);

    if ($offer) {
        $offer->delete();
        $response = [
            'message' => ['sucess' => [trans('messages.deleted')]],
        ];
        return $this->responseSuccess($response);
    } else {
        $response = [
            'message' => ['error' => [trans('messages.deleted_faild')]],
        ];
        return $this->responseFaild($response);
    }

}
//Return all offers of specific order by it's id in offers DB
public function allOffers($id)
{
    $offers = OrderOffer::where('order_id', $id)->with('user_data')->get();

    $data = [];
    foreach ($offers as $offer) {

        $offer = $offer;
        $offerData["id"] = $offer['id'];
        $offerData["order_id"] = $offer['order_id'];
        $offerData["shipping"] = $offer['shipping'] + $offer['shipping'] * \App\Models\Setting::find(1)->commission_percent /100;
        $offerData["lat"] = $offer['lat'];
        $offerData["lng"] = $offer['lng'];
        $offerData["status"] = $offer['status'];
        $offerData["created_at"] = $offer['created_at'];
        $offerData["user_id"] = $offer['user_id'];
        $offerData["user_data"] = new UserResource($offer['user_data']);

        $data[] = $offerData;
    }

    $response = [
        'data' => $data,

    ];

    return $this->responseSuccess($response);
}
/// filter Clients'  Orders throw [user_type=> must be 2 , user_id [to get client data] and status [confirmed or not]   ]
public function filter(Request $request)
{
    if($request->package_type){
        if ($request->user_type == 2) {
            if ($request->status == 6) {
                $orders = Order::with('items')->where('user_id', $request->user_id)->where('package_type', $request->package_type)->orderBy('id', 'desc')->paginate(10);
            } else {
                if ($request->status == 2) {
                    $orders = Order::with('items')->whereIn('status', [0, 1, 2])->where('user_id', $request->user_id)->where('package_type', $request->package_type)->orderBy('id', 'desc')->paginate(10);
                } else {
                    $orders = Order::with('items')->where('status', $request->status)->where('user_id', $request->user_id)->where('package_type', $request->package_type)->orderBy('id', 'desc')->paginate(10);
                }
            }
        } else {
            if ($request->status == 6) {
                $orders = Order::with('items')->where('shipper_id', $request->user_id)->where('package_type', $request->package_type)->orderBy('id', 'desc')->paginate(10);
            } else {
                if ($request->status == 2) {
                    $orders = Order::with('items')->whereIn('status', [0, 1, 2])->where('shipper_id', $request->user_id)->where('package_type', $request->package_type)->orderBy('id', 'desc')->paginate(10);
                } else {
                    $orders = Order::with('items')->where('status', $request->status)->where('shipper_id', $request->user_id)->where('package_type', $request->package_type)->orderBy('id', 'desc')->paginate(10);
                }
            }
        }
    
    }else{
        if ($request->user_type == 2) {
            if ($request->status == 6) {
                $orders = Order::with('items')->where('user_id', $request->user_id)->orderBy('id', 'desc')->paginate(10);
            } else {
                if ($request->status == 2) {
                    $orders = Order::with('items')->whereIn('status', [0, 1, 2])->where('user_id', $request->user_id)->orderBy('id', 'desc')->paginate(10);
                } else {
                    $orders = Order::with('items')->where('status', $request->status)->where('user_id', $request->user_id)->orderBy('id', 'desc')->paginate(10);
                }
            }
        } else {
            if ($request->status == 6) {
                $orders = Order::with('items')->where('shipper_id', $request->user_id)->orderBy('id', 'desc')->paginate(10);
            } else {
                if ($request->status == 2) {
                    $orders = Order::with('items')->whereIn('status', [0, 1, 2])->where('shipper_id', $request->user_id)->orderBy('id', 'desc')->paginate(10);
                } else {
                    $orders = Order::with('items')->where('status', $request->status)->where('shipper_id', $request->user_id)->orderBy('id', 'desc')->paginate(10);
                }
            }
        }

    }
   
    $data = [];
   
        foreach ($orders as $order) {
            $OrderData['id'] = $order->id;
            $OrderData['code'] = $order->code;
            $OrderData['price'] = $order['price'];
            $OrderData['package_type'] = $order['package_type'];
            $OrderData['payment_status'] = $order['payment_status'];
            $OrderData['type_image'] = $order->type_image();
            $OrderData['type_title'] = $order->type_title();
            $OrderData['created_at'] = $order->created_at;
            $OrderData['item_count'] = count($order->items);
            $OrderData['status']     = $order->status;
            $OrderData['type'] = $order['type'];
            $OrderData['destination_lat'] = $order['destination_lat'];
            $OrderData['destination_lng'] = $order['destination_lng'];

            $data[] = $OrderData;

        }

    return $this->responseSuccessPages(['data' => $data], $orders);
}


 public function charge_wallet(Request $request)
{
    $this->validate($request, [
        'user_id' => 'required|numeric|exists:users,id',
        'order_id' => 'required|numeric|exists:orders,id',
        'amount' => 'required',
    ]);

    $this->notificationServ->notifyChargeWallet(
        ['fcm', 'db'],
        ['order_id' => $request->order_id, 'user_id' => $request->user_id, 'amount' => $request->amount]
    );

    $user = User::find($request->user_id);
    $user->amount = $user->amount + $request->amount;
    $user->save();
    $order = Order::find($request->order_id);
    $order->charge_wallet = $order->charge_wallet + $request->amount;
    $order->save();

    // send success
    $response = [
        'message' => ['sucess' => [trans('messages.added')]],

    ];
    return $this->responseSuccess($response);

}


//enable Driver to add update deliver costs and notfy client with it 
public function deserve_more(Request $request)
{
    $this->validate($request, [
        //'offer_id' => 'required|numeric|exists:order_offers,id',
        'order_id' => 'required|numeric|exists:orders,id',
        'MoreMoney' => 'required|numeric',
    ]);

    $order = Order::findOrFail($request->order_id);
   $order->deserve_more = 1;
   $order->deserve_more_cost = $request->MoreMoney;
   $order->price = $order->price + $order->deserve_more_cost;
   $order->commission= $order->price * (\App\Models\Setting::find(1)->commission_percent / 100);
   $ordershipping = $order->price  +  $order->commission;
   $order->addition_service_cost = ( $order->price  +  $order->commission ) * (\App\Models\Setting::find(1)->commission_percent / 100);
   $order->total =  $ordershipping + $order->addition_service_cost + $order->deserve_more_cost - $order->discount;
   $order->save();
    // $order->update(['deserve_more' => 1, 'deserve_more_cost' => $request->MoreMoney]);
   
    $this->notificationServ->notifyDeserveMore(
        ['fcm', 'db'],
        ['order_id' => $request->order_id]
    );

    // send success
    $response = [
        'message' => ['sucess' => [trans('messages.added')]],

    ];
    return $this->responseSuccess($response);

}


// change order status if the order has reach or in it's way and notfy order owner with it
public function changeStatus(Request $request)
{
   
    $this->validate($request, [
        'order_id' => 'required|numeric|exists:orders,id',
        'price' => 'nullable|numeric',
        'status' => 'required|numeric',
        'image' => 'nullable|file|image|mimes:jpeg,png,gif,jpg|max:1024',

    ]);

    $order = Order::find($request->order_id);
    if (isset($request->price)) {
        // $order->update(['price' => $request->price]);
        $order->price = $request->price;
        $order->commission= $order->price * (\App\Models\Setting::find(1)->commission_percent / 100);
        $ordershipping = $order->price  +  $order->commission;
        $order->addition_service_cost = ( $order->price  +  $order->commission ) * (\App\Models\Setting::find(1)->commission_percent / 100);
        $order->total =  $ordershipping + $order->addition_service_cost - $order->discount;
        $order->save();
    }
    if (isset($request->image)) {
        $path = $this->storeFile($request, [
            'fileUpload' => 'image',
            'folder' => Order::FILE_FOLDER,
            'recordId' => $request->order_id . '_image',
        ]);

        $order->update(['invoice' => $path]);
    }

    $order->update(['status' => $request->status]);

    if ($request->status == 4) {
        $order->update(['delivery_date' => \Carbon\Carbon::now()]);
        
   }
   
    if ($request->status == 3 || $request->status == 4) {
        $this->notificationServ->notifyOrderStatus(
            ['fcm', 'db'],
            ['order_id' => $request->order_id, 'status' => $request->status]
        );
    }
    $response = [
        'message' => ['sucess' => [trans('messages.updated')]],
        'data' => [$order]
    ];
    
    return $this->responseSuccess($response);
}

//change payment Method by any method [ if client can pay by his wallet if  his wallet has enough money or return error message to concern him]
public function changePayment(Request $request)
{
    $this->validate($request, [
        'order_id' => 'required|numeric|exists:orders,id',
        'payment_type' => 'required|numeric',

    ]);

    $order = Order::find($request->order_id);
    $user = User::find($order->user_id);
    
 
    if(!$order){
        $response = ['message' => trans('messages.added_faild')];
        return $this->responseFaild($response);
    }
    if(!$user){
        $response = ['message' => trans('messages.added_faild')];
        return $this->responseFaild($response);
    }
        if ($request->payment_type == 1) {
            $order->payment_type = $request->payment_type;
            $order->payment_status = 1;
            $order->save();
            $driver = User::find($order->shipper_id);
            $appCommission = $order->commission + $order->addition_service_cost;
            $driver->amount = $driver->amount - $appCommission;
            $driver->save();
        }
    if ($request->payment_type == 2) { // if user want to pay by his wallet
        if ($order->total < $user->amount) {

            $data['title'] = 'wallent_paid';
            $data['amount'] = $order->total;
            $data['date'] = \Carbon\Carbon::now();
            $data['description'] = "decrease_amount";
            $data['user_id'] = $order->user_id;
            $data['order_code'] = $order->code;
            Transaction::create($data);

            $user->amount = $user->amount - $order->total;
            $user->save();
            $order->payment_type = $request->payment_type;
            $order->payment_status = 1;
            $order->save();
             //Driver
             if($order->shipper_id){   // in souq 
               
                $data['title'] = 'Driver_get_paid';
                $data['amount'] = $order->price;
                $data['date'] = \Carbon\Carbon::now();
                $data['description'] = "increase_amount";
                $data['user_id'] = $order->shipper_id;
                $data['order_code'] = $order->code;
                Transaction::create($data);
                $driver = User::find($order->shipper_id);
                $driver->amount = $driver->amount + $order->price;
                $driver->save();
                $this->notificationServ->notifyChargeWallet(
                    ['fcm', 'db'],
                    ['order_id' => $order->id, 'user_id' => $driver->id, 'amount' => $order->price]
                );
            }
        } else {
            $response = ['message' => ['error' => [trans('messages.balance_not_enough')]]];
            return $this->responseSuccess($response);
        }

    }
    // 8a8294174d0595bb014d05d82e5b01d2
    if ($request->payment_type == 3) { 
        $hyper = $order->total * 0.02 ;
        $total =  $order->total + $hyper  ;
        $order->payment_type = $request->payment_type;
        $order->total = $total;
        $order->save();
        
    }
    $this->notificationServ->notifychangePayment(
        ['fcm', 'db'],
        ['order_id' => $request->order_id]
    );

    $response = ['message' => ['sucess' => [trans('messages.updated')]],
        "data"=>[$order]
];
    return $this->responseSuccess($response);
}

//add Coupon to Order and check if coupon is still valid or not throw [expire_date , number of use]
public function addCoupon(Request $request)
{
    $this->validate($request, [
        'order_id' => 'required|numeric|exists:orders,id',
        'coupon' => 'required|exists:coupons,coupon',

    ]);

    $order = Order::with(['offer'])->findOrFail($request->order_id);
    $coupon = Coupon::where('coupon', $request->coupon)->first();
    $numOfUse = Order::where('coupon', $request->coupon)->count();
    $is_used = Order::where(['coupon' => $request->coupon, 'user_id' => $order->user_id])->first();
    if ($coupon) {
        if ($coupon->expire_date > \Carbon\Carbon::now() && $coupon->limit > $numOfUse) {
            if (!$is_used) {
                if ($shipping = $order['offer']) {
                    // $dicount = $order->commission * $coupon->amount / 100;
                    $dicount = $coupon->amount ;
                    $order->update(['coupon' => $coupon->coupon, 'discount' => number_format($dicount,2)]);
                    $response = ['message' => ['sucess' => [trans('messages.added')]]];
                    return $this->responseSuccess($response);
                }
            } else {
                $response = ['message' => trans('messages.used')];
                return $this->responseFaild($response);
            }
        } else {
            $response = ['message' => trans('messages.expired')];
            return $this->responseFaild($response);
        }
    }

    $response = ['message' => trans('messages.added_faild')];
    return $this->responseFaild($response);
}


    public function hyperRequest($amount, $mid, $cust_mail, $cust_name, $cust_add, $brands)
    {
       // $amount=number_format($amount,2);
       $amount=str_replace(",","",$amount);
      /* if($brands=="APPLEPAY")
       {
           $url="https://test.oppwa.com/v1/checkouts";
       }
       else{*/
        // $url = "https://oppwa.com/v1/checkouts";
        $url = "https://eu-test.oppwa.com/v1/checkouts";
    
      // }
        if ($brands == "MADA")
        {
            $entityId = "8ac7a4c981faa8650181fe7436b3054e";
            $au="OGFjN2E0Yzk4MWZhYTg2NTAxODFmZTc0MzcyYjA1NTJ8Z1p4cktBN2hoNg";
        }
        else if($brands=="APPLEPAY")
        {
            $entityId="8ac7a4c981faa8650181fe7436b3054e";
            $au="OGFjN2E0Yzk4MWZhYTg2NTAxODFmZTc0MzcyYjA1NTJ8Z1p4cktBN2hoNg";
        }
        else
        {
            $entityId = "8ac7a4c981faa8650181fe7436b3054e";
            $au="OGFjN2E0Yzk4MWZhYTg2NTAxODFmZTc0MzcyYjA1NTJ8Z1p4cktBN2hoNg";
        }
        $data = "entityId=$entityId" . "&amount=$amount" . "&currency=EUR" . "&merchantTransactionId=$mid" . "&customer.surname=$cust_name" . "&customer.givenName=$cust_name" . "&customer.email=$cust_mail" . "&billing.street1=$cust_add" . "&billing.city=Riyadh" . "&billing.state=sudia" . "&billing.country=SA" . "&billing.postcode=42391" . "&paymentType=DB";
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer '.$au
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
    
        if (curl_errno($ch))
        {
            return curl_error($ch);
        }
        curl_close($ch);
        return json_decode($responseData , true);
    }
    public function hyperView($checkoutId){
        return 'https://www.souq.appsjannah.com/api/v1/orders/hyperpayview/'.$checkoutId;
    
    }
    public function hyperpayview($checkoutId){
        return view('hyper' , ['checkoutId' => $checkoutId]);
    }
    // public function hypersucess($checkout_id){
    //     $resp = response()->json([
    //         'code'=>200,
    //         'status'=>'sucess',
    //         'message'=>'عمليه الدفع تمت بنجاح'
    //     ]);
    //     return $res;
    // }
    public function requeststatus()
    {
    
        $url="https://eu-test.oppwa.com/v1/checkouts/" . $_GET['id'] . "/payment";
    
       $entityId = "8ac7a4c981faa8650181fe7436b3054e";
       $au="OGFjN2E0Yzk4MWZhYTg2NTAxODFmZTc0MzcyYjA1NTJ8Z1p4cktBN2hoNg";
        $url .= "?entityId=$entityId";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer '.$au
        ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch))
        {
            return curl_error($ch);
        }
        curl_close($ch);
        $responseData =  json_decode( $responseData, true);
        if($responseData['result']['code'] =="200.300.404"){
            return response()->json([
                'code' => 200,
                'status'=> 'error',
                'message'=>'هناك خطأ ما يرجي المحاوله مره اخري',
                'data'=>$responseData
            ]);
           
        }else{
            
            $order = Order::find($responseData['merchantTransactionId']);
            $order->payment_status = 1;
            $order->save();
            $offer = OrderOffer::where(['order_id' => $order->id, 'status' => 2]);
            if($offer){
                if($order->shipper_id){
                  
                    $driver_pay = $order->price  + $offer->shipping ;
                    $data['title'] = 'Driver_get_paid';
                    $data['amount'] = $driver_pay;
                    $data['date'] = \Carbon\Carbon::now();
                    $data['description'] = "increase_amount";
                    $data['user_id'] = $order->shipper_id;
                    $data['order_code'] = $order->code;
                    Transaction::create($data);
                    $driver = User::find($order->shipper_id);
                    $driver->amount = $driver->amount + $driver_pay;
                    $driver->save();
                    $this->notificationServ->notifyChargeWallet(
                        ['fcm', 'db'],
                        ['order_id' => $order->id, 'user_id' => $driver->id, 'amount' =>$driver_pay]
                    );
                }
            }else{
                if($order->shop_id != null){
                    $driver_pay = $order->price;
                    $data['title'] = 'Driver_get_paid';
                    $data['amount'] = $driver_pay;
                    $data['date'] = \Carbon\Carbon::now();
                    $data['description'] = "increase_amount";
                    $data['user_id'] = $order->shipper_id;
                    $data['order_code'] = $order->code;
                    Transaction::create($data);
                    $driver = User::find($order->shipper_id);
                    $driver->amount = $driver->amount + $driver_pay;
                    $driver->save();
                    $this->notificationServ->notifyChargeWallet(
                        ['fcm', 'db'],
                        ['order_id' => $order->id, 'user_id' => $driver->id, 'amount' => $driver_pay]
                    );
                }
            }
            
            return response()->json([
                'code' => 200,
                'status'=> 'success',
                'message'=> "تم الدفع بنجاح",
                'data'=>$order
            ]);
           
        }
            
      
    }
  

public function testApi(){

 
 $response = Http::post('https://mobrdemo.elm.sa/api/Order/create', [
"credential" =>[
 "companyName"=>"kafu",
 "password"=>"XVjx93xxHfM8bKMUPAxasdxBGRo6qzNjEPCgx13Q66oBpluGcinYXRxsk7kQ8o1N"
    ],
 "order"=> [
 "orderNumber"=> "1",
 "authorityId"=> "NV25GlPuOnQ=",
 "deliveryTime"=> "2020-04-08T12:43:33.369Z",
 "regionId"=> "NV25GlPuOnQ=",
 "cityId"=>"NV25GlPuOnQ=",
 "coordinates"=> "123,321",
 "storetName"=> "Halol",
 "storeLocation"=> "456,652",
 "categoryId"=>"NV25GlPuOnQ=",
 "orderDate"=>"2020-04-19T12:07:10.723Z"
  ]
])->throw()->json();
return $this ->getErrorCode($response['errorCodes'][0]);
//return $response['errorCodes'];
}



}