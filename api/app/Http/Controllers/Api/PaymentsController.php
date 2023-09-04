<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointement;
use App\Models\User;
use App\Models\Product;
use App\Models\Payment;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;

class PaymentsController extends Controller

{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->page;
        $per_page= $request->per_page;
        $filter= $request->filter;
        $order_by = $request->order_by;
        $order_id = $request->order_id;

        return Payment::where('title', 'LIKE', "%{$filter}%")
            ->orWhere('content', 'LIKE', "%{$filter}%")
            ->orderBy($order_id, $order_by)
            ->paginate($per_page);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


    $post = Payment::create($request->all());


    return response()->json(['post'=>$post], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $appointements = Payment::find($id);
        $userid = $appointements->user_id;
        $users = DB::table('users')->where('id','=', $userid )
        ->select('users.*')
        ->get();

    


    return response()->json(['users'=>$users, 'appointement'=>  $appointements],200);
   // return response()->json(User::find($id), 200);
  
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $post = Payment::findOrFail($id);
        $post->update($request->all());
        return response()->json($post, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      Payment::findOrFail($id)->delete();
        return response('Deleted Successfully', 200);
    }

    public function paymentsByUserPerpage($id, Request $request)
    {
        $page = $request->page;
        $per_page = $request->per_page;
        $order_by = $request->order_by;
        $order_id = $request->order_id;
        $filter = $request->filter;

        if($filter){
            return Payment::where('edited_by', $id)
            ->orWhere('user_id','=',$id)
            ->orderBy($order_id, $order_by)
            ->paginate($per_page);
        }else{
            return Payment::where('edited_by', $id)
            ->orWhere('user_id','=',$id)
            ->orderBy($order_id, $order_by)
            ->paginate($per_page);
        }
    }

  
    public function paymentsByUser($id, Request $request)
    {
      $appointements = DB::table('payments')
       ->where('payments.user_id',$id)
       ->orderBy("start_at", "desc")
       ->get();
       return response()->json($appointements, 200);

    }


    public function getTokenHelloAsso()
    {
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.helloasso.com/oauth2/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'client_id=400085e40b2444f9a22439af4b968e7d&client_secret=hOtzF1jrSqR779tKh%2F%2Br9dTRsupfPZLu&grant_type=client_credentials',
      ));
      $response = curl_exec($curl);
       curl_close($curl);
       //echo $response;
       return response()->json($response, 200);
    }



    public function return(Request $request)
    {
        $checkoutIntentId = $request->checkoutIntentId;
        $code= $request->code;
     //   $post =  DB::insert('insert into payments (checkoutIntentId, userid, code) values (?, ?, ?)',['Niyati', 'niyati@gmail.com', 19]);
        return response()->json(['checkoutIntentId'=>$post],200);
     
    }






   

}
