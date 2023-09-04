<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointement;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Mail\AppointementEmail;
use App\Mail\AppointementonemonthEmail;
use App\Mail\AppointementtowmonthEmail;

use Carbon\Carbon;

class AppointementsController extends Controller

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

        return Appointement::where('title', 'LIKE', "%{$filter}%")
            ->orWhere('firstname_activite', 'LIKE', "%{$filter}%")
            ->orWhere('lastname_activite', 'LIKE', "%{$filter}%")
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


    $post = Appointement::create($request->all());

    $input = $request->all();
    $idproduct = $input['idproduct'];



    $product = Product::find($idproduct);
    $place = $product->nb_free;

    $product->nb_free = $place-1;
    $product->save();

    return response()->json(['post'=>$post, 'product'=> $product,  'placelibre'=> $place], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $appointements = Appointement::find($id);
        $userid = $appointements->user_id;
        $techid = $appointements->edited_by;
        $users = DB::table('users')->where('id','=', $userid )
        ->select('users.*')
        ->get();

        $techs = DB::table('users')->where('id','=', $techid )
        ->select('users.*')
        ->get();


    return response()->json(['users'=>$users, 'techs'=> $techs,'appointement'=>  $appointements],200);
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
        $post = Appointement::findOrFail($id);
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
        Appointement::findOrFail($id)->delete();
        return response('Deleted Successfully', 200);
    }

    public function appointementsByUser($id, Request $request)
    {
        $page = $request->page;
        $per_page = $request->per_page;
        $order_by = $request->order_by;
        $order_id = $request->order_id;
        $filter = $request->filter;

        if($filter){
            return Appointement::where('edited_by', $id)
            ->orWhere('user_id','=',$id)
            ->orderBy($order_id, $order_by)
            ->paginate($per_page);
        }else{
            return Appointement::where('edited_by', $id)
            ->orWhere('user_id','=',$id)
            ->orderBy($order_id, $order_by)
            ->paginate($per_page);
        }
    }

    public function appointementsByUserShort($id, Request $request)
    {
        $page = $request->page;
        $per_page = 10;
        $order_by = 'desc';
        $order_id = 'id';

        return Appointement::where('edited_by', $id)
        ->orWhere('user_id','=',$id)
        ->orderBy($order_id, $order_by)
        ->paginate($per_page);
    }



    public function appointementByUser($id, Request $request)
    {
      $appointements = DB::table('appointements')
       ->where('appointements.user_id',$id)
       ->orderBy("start_at", "desc")
       ->get();
       return response()->json($appointements, 200);

    }


    public function gallerieBypost($id, Request $request)
    {
      $appointements = DB::table('image_galleries')
       ->where('image_galleries.postid',$id)
       ->get();
       return response()->json($appointements, 200);

    }

 
}
