<?php

namespace App\Traits;
use Illuminate\Support\Facades\Http;

trait TawseelOrder
{
//TAWSEEL ORDER
    public function CreateOrderInTawseel($request,$order){
        if($order->shop_name == null){
            $order->shop_name= "unknown";
        }
        
        $TawseelResponse = Http::post('https://mobrdemo.elm.sa/api/Order/create', [
            "credential" =>[
             "companyName"=>"kafu",
             "password"=>"XVjx93xxHfM8bKMUPAxasdxBGRo6qzNjEPCgx13Q66oBpluGcinYXRxsk7kQ8o1N"
                ],
             "order"=> [
             "orderNumber"  => $order->id,
             "authorityId"  => $request->authorityId,
             "deliveryTime" => gmdate('Y-m-d H:i:s', strtotime($order->created_at)),
             "regionId"     => $request->regionId,
             "cityId"       => $request->cityId,
             "coordinates"  => $order->destination_lat .",". $order->destination_lng,
             "storetName"   => $order->shop_name,
             "storeLocation"=> $order->source_lat .",". $order->source_lng,
             "categoryId"   => $request->categoryId,
             "orderDate"    => gmdate('Y-m-d H:i:s', strtotime($order->created_at))
            ]
        ])->throw()->json();
   
            if($TawseelResponse['status'] == false){
                $data ['tawseelStatus']        = false;
                $data['tawseelMessage'] = $this ->getErrorCode($TawseelResponse['errorCodes'][0]);
                    return   $data;
       } else{
                $data ['tawseelStatus']        = true;
                $data['refrenceCode'] =  $TawseelResponse['data']['refrenceCode'];
                    return   $data;
       } 
    }

    public function CancelOrderInTawseel( $refrenceCode , $cancelationReasonId ){

        $TawseelResponse = Http::post('https://mobrdemo.elm.sa/api/Order/cancel', [
            "credential" =>[
            "companyName"=>"kafu",
            "password"=>"XVjx93xxHfM8bKMUPAxasdxBGRo6qzNjEPCgx13Q66oBpluGcinYXRxsk7kQ8o1N"
                ],
            "refrenceCode"=> $refrenceCode,
            "cancelationReasonId"=> $cancelationReasonId

            ])->throw()->json();
        if($TawseelResponse['status'] == false){
                $data ['tawseelStatus']        = false;
                $data['tawseelMessage'] = $this ->getErrorCode($TawseelResponse['errorCodes'][0]);
                    return   $data;
       }
       else{
                $data ['tawseelStatus'] = true;
                $data ['response']      =  $TawseelResponse;
                    return   $data;
        }

    }
    
    public function ExecuteOrderInTawseel($request , $order){
        //excute order  in Tawseel
        $TawseelResponse = Http::post('https://mobrdemo.elm.sa/api/Order/execute', [
            "credential" =>[
            "companyName"=>"kafu",
            "password"=>"XVjx93xxHfM8bKMUPAxasdxBGRo6qzNjEPCgx13Q66oBpluGcinYXRxsk7kQ8o1N"
                ],
                "orderExecutionData" => [
                    "refrenceCode"   => $order->refrenceCode,
                    "executionTime"   => gmdate('Y-m-d H:i:s', strtotime($order->created_at)),
                    "paymentMethodId" => $request->paymentMethodId,
                    "price"           => $order->total
                    ]

        ])->throw()->json();
    
    if($TawseelResponse['status'] == false){
        $data ['tawseelStatus']        = false;
        $data['tawseelMessage'] = $this ->getErrorCode($TawseelResponse['errorCodes'][0]);
            return   $data;
    }
    else{
            $data ['tawseelStatus'] = true;
            $data ['response']      =  $TawseelResponse;
                return   $data;
    }
}
    public function AssignDriverToOrderInTawseel($refrenceCode , $idNumber){
   //assign driver to order in Tawseel
        $TawseelResponse = Http::post('https://mobrdemo.elm.sa/api/Order/assign-driver-to-order', [
            "credential" =>[
            "companyName"=>"kafu",
            "password"=>"XVjx93xxHfM8bKMUPAxasdxBGRo6qzNjEPCgx13Q66oBpluGcinYXRxsk7kQ8o1N"
                ],
            "refrenceCode" => $refrenceCode,
            "idNumber"      => $idNumber

        ])->throw()->json();
    
    if($TawseelResponse['status'] == false){
        $data ['tawseelStatus']        = false;
        $data['tawseelMessage'] = $this ->getErrorCode($TawseelResponse['errorCodes'][0]);
            return   $data;
    }
    else{
            $data ['tawseelStatus'] = true;
            $data ['response']      =  $TawseelResponse;
                return   $data;
    }
  }
}
