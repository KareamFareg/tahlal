<?php

namespace App\Http\Controllers\Api;

use App\Helpers\UtilHelper;
use App\Http\Controllers\AdminController;
use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Http\Request;

class CountryController extends AdminController
{

    private $countryServ;

    public function __construct(CountryService $countryService)
    {

        $this->countryServ = $countryService;

    }

    public function index(Request $request)
    {

        $trans = 'ar';
        if ($request->isMethod('post')) {
            $trans = $request->trans;
        }
          $countries = $this->countryServ->getAll($trans);
        $temp = [];
          $countries = UtilHelper::buildTreeRoot($countries, null, $temp, 1, 0);
        return  response()->json([
            "code"=>200,'status' => "sucess",
            "message"=>"all countries",
            "data"=>$countries
        ]);
    }



}
