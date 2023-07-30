<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\TestRequest;
use App\Models\Test;

class TestController extends Controller
{

    public function form(Request $request) // TestRequest $request
    {

      $data = Test::all();
      $trans = 'ar';
      // foreach ($data as $key => $d) {
      // dd($d->question_data());
      // }

      return view('admin.test',compact(['data','trans']));

      // if ($request->name) {
      //   dd('rname');
      // }
      //
      // if ($request->filled('name')) {
      //     dd($request->name);
      // }
      //
      //
      // dd('dddddddddddddd');
      //   return view('admin.form');
    }

    public function post(Request $request) // TestRequest $request
    {

      $faq = Test::where('id',1)->firstorfail();

      $data = array_merge($faq->question , $request->question);

      $faq->update(['question'=>$data]);


    }

}
