<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use App\Models\Notification;
use App\Models\Invoices;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\DB;

use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use App\Mail\Form1Mail;
use App\Mail\Form3Mail;
use PDF;

class InvoicesController extends Controller
{
        /**
         *
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function index(Request $request)
        {
            $page = $request->page;
            $per_page= $request->per_page;
            $order_id= $request->order_id;
            $filter= $request->filter;
            $order_by = $request->order_by;
            $category = $request->category;


            $page = $request->page;
            $per_page= $request->per_page;
            $filter= $request->filter;
            $order_by = $request->order_by;
            $order_id = $request->order_id;
    
            return Invoices::where('InvoiceID', 'LIKE', "%{$filter}%")
                ->orderBy($order_id, $order_by)
                ->paginate($per_page);
    
          
        }
    
        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function create(Request $request)
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
            $input = $request->all();
            $DueDate= Carbon::now()->addMonth();
            $input['DueDate']=$DueDate;

            $lastid = DB::table('invoices')->get()->last()->InvoiceID;
            $input['InvoiceID']= $lastid+1;
    
            $invoice = Invoices::create($input);
            return response()->json($invoice, 201);
        }
    
        /**
         * Display the specified resource.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function show($id)
        {
            $invoice =Invoices::find($id);
            $items = DB::table('appointements')
            ->where('idinvoice',$id)
         //->orderBy("start_at", "desc")
            ->get();
     
            return response()->json([ 'invoice'=>$invoice,'items'=>$items ],200);
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
            $user = Invoices::findOrFail($id);
            $user->update($request->all());
            return response()->json($user, 200);
    
        }
    
        /**
         * Remove the specified resource from storage.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function destroy($id)
        {
            Invoices::findOrFail($id)->delete();
            return response()->json('Deleted Successfully', 200);
        }
    

          public function userByrole(Request $request)
        {
            $page = $request->page;
            $per_page= $request->per_page;
            $order_id= $request->order_id;
            $filter= $request->filter;
            $order_by = $request->order_by;
    
            return User::where('role', 'LIKE', "%{$filter}%")
                ->orderBy($order_id, $order_by)
                ->paginate($per_page);
        }
    
    
        public function invoiceById($id, Request $request)
        {
            $page = $request->page;
            $per_page = 100;
            $order_by = 'asc';
            $order_id = 'id';
    
            return Invoices::where('InvoiceID', $id)
            ->orderBy($order_id, $order_by)
            ->paginate($per_page);


           
        }
    
  
    public function invoiceview($id,Request $request)
    {
        $idinvoice=$id;
        $invoicesrow = DB::table('invoices')->where('InvoiceID',$idinvoice)->get();
        $userid=$invoicesrow[0]->CustomerID;
        $invoiceid=$invoicesrow[0]->id;

        $usersrow  = DB::table('users')->where('id','=',$userid)->select('users.*')->get();

        $company=$usersrow[0]->company;
        $firstname=$usersrow[0]->firstname;
        $lastname=$usersrow[0]->lastname;
        $address=$usersrow[0]->address;
        $cp=$usersrow[0]->cp;
        $city=$usersrow[0]->city;
        $phone_mobile=$usersrow[0]->phone_mobile;

        $shipping_phone=$usersrow[0]-> shipping_phone;
        $billing_phone=$usersrow[0]->billing_phone;
        $tva_number=$usersrow[0]->tva_number;
        $siret_number=$usersrow[0]->siret_number;
        $due_date=$invoicesrow[0]->DueDate;
        $due_date = Carbon::createFromFormat('Y-m-d',$due_date)->format('d/m/Y');
        $client = new Party([
        ]);

        $customer = new Party([
            'company'          => $company,
            'firstname'          => $firstname,
            'lastname'          => $lastname,
            'shipping_address'      =>  $address,
            'shipping_cp'          => $cp ,
            'shipping_city'          => $city ,
            'shipping_phone'          => $phone_mobile ,
            'billing_phone'          => $billing_phone ,
            'tva_number'          => $tva_number ,
            'siret_number'          => $siret_number,
            'due_date'          => $due_date 
        ]);



        $itemsrow  = DB::table('appointements')->where('idinvoice','=',$invoiceid)
        ->select('appointements.*')
        ->get();
        
        $items = [];
        $total = 0;
        foreach (  $itemsrow as $invoiceitem ) {
             array_push( $items,  (new InvoiceItem())
             ->title($invoiceitem->title)
             ->description($invoiceitem->content)
             ->pricePerUnit($invoiceitem->price)
             ->quantity(1)
             );

             $total = $total+($invoiceitem->price);
        }


    
        $notes = [
            'your multiline',
            'additional notes',
            'in regards of delivery or something else',
        ];
        $notes = implode("<br>", $notes);
        $path = public_path('invoices');
        $date = Carbon::now()->timezone('Europe/Stockholm')->toDateTimeString();
        $randomtitle=random_int(100000,999999);
        $fileName =  $randomtitle . '.' . 'pdf' ;


        $invoice = Invoice::make('receipt')
            ->series('Facture')
            // ability to include translated invoice status
            // in case it was paid
            ->status(__('invoices::invoice.paid'))
            ->sequence($idinvoice)
            ->serialNumberFormat('{SEQUENCE}')
            ->seller($client)
            ->buyer($customer)
            ->date(now())
            ->dateFormat('d/m/Y')
           // ->payUntilDays($invoicesrow[0]->DueDate)
            ->currencySymbol('€')
            ->currencyCode('EUR')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->filename($client->name . ' ' . $customer->name)
            ->addItems($items)
            ->notes($notes)
            ->taxRate(0)
            ->logo(public_path('images/logo-invoice.png'))
            ->save('public');
    
       $link = $invoice->url();
       return $invoice->stream();
    }



    public function invoicesend($id,Request $request){

        
  
        $invoicesrow = DB::table('invoices')->where('id',$id)->get();


        $userid=$invoicesrow[0]->CustomerID;
        $IID=$invoicesrow[0]->InvoiceID;

        $invoiceid=$id;

        $usersrow  = DB::table('users')->where('id','=',$userid)->select('users.*')->get();
        $email=$usersrow[0]->email;
        $company=$usersrow[0]->company;
        $firstname=$usersrow[0]->firstname;
        $lastname=$usersrow[0]->lastname;
        $address=$usersrow[0]->address;
        $cp=$usersrow[0]->cp;
        $city=$usersrow[0]->city;
        $phone_mobile=$usersrow[0]->phone_mobile;

        $shipping_phone=$usersrow[0]-> shipping_phone;
        $billing_phone=$usersrow[0]->billing_phone;
        $tva_number=$usersrow[0]->tva_number;
        $siret_number=$usersrow[0]->siret_number;
        $due_date=$invoicesrow[0]->DueDate;
        $due_date = Carbon::createFromFormat('Y-m-d',$due_date)->format('d/m/Y');
        $client = new Party([
        ]);

        $customer = new Party([
            'company'          => $company,
            'firstname'          => $firstname,
            'lastname'          => $lastname,
            'shipping_address'      =>  $address,
            'shipping_cp'          => $cp,
            'shipping_city'          => $city ,
            'shipping_phone'          => $phone_mobile ,
            'billing_phone'          => $billing_phone ,
            'tva_number'          => $tva_number ,
            'siret_number'          => $siret_number,
            'due_date'          => $due_date 
        ]);



        $itemsrow  = DB::table('appointements')->where('idinvoice','=',$invoiceid)
        ->select('appointements.*')
        ->get();
        
        $items = [];
        $total = 0;
        foreach (  $itemsrow as $invoiceitem ) {
             array_push( $items,  (new InvoiceItem())
             ->title($invoiceitem->title)
             ->description($invoiceitem->content)
             ->pricePerUnit($invoiceitem->price)
             ->quantity(1)
             );

             $total = $total+($invoiceitem->price);
        }

        $notes = [
            'your multiline',
            'additional notes',
            'in regards of delivery or something else',
        ];
        $notes = implode("<br>", $notes);

        
      //  $path = public_path('invoices');



        $date = Carbon::now()->timezone('Europe/Stockholm')->toDateString();
        $randomtitle=random_int(100000,999999);
        $fileName =$date .'.'. $randomtitle . '.' . 'pdf' ;


          
        $invoice = Invoice::make('receipt')
        ->series('Facture')
        // ability to include translated invoice status
        // in case it was paid
        ->status(__('invoices::invoice.paid'))
        ->sequence($IID)
        ->serialNumberFormat('{SEQUENCE}')
        ->seller($client)
        ->buyer($customer)
        ->date(now())
        ->dateFormat('d/m/Y')
       // ->payUntilDays($invoicesrow[0]->DueDate)
        ->currencySymbol('€')
        ->currencyCode('EUR')
        ->currencyFormat('{SYMBOL}{VALUE}')
        ->currencyThousandsSeparator('.')
        ->currencyDecimalPoint(',')
        ->filename($client->name . ' ' . $customer->name)
        ->addItems($items)
        ->notes($notes)
        ->taxRate(0)
        ->logo(public_path('images/logo-invoice.png'))
        ->save('public');



        $path = public_path('pdf/');
        $pdf = PDF::loadView('pdf.default', ['invoice' => $invoice])->setPaper('a4', 'portrait');
        $pdf->save($path . '/' . $fileName);

       
        $user = ['email' => "",'password' => "",];
        Mail::to($email)->send(new Form1Mail($pdf));

     /*
        $notif = Notification::create([
             'title' => "PDF",
             'content' => "Pdf à télécharger",
             'edited_by' => $request->id,
             'link' => 'pdf/'.$fileName,
             'category' => "PDF",
             'view'=>0
         ]);

         */
     
        return response()->json([
         'message' => 'Successfully created',$fileName
     ]);
    }


    public function updateInvoiceId($id, Request $request)
    {
       

      $invoice = Invoices::findOrFail($id);
      $input=$request->all();
      //$price=$request->ItemPrice;
    //  $qte=$request->Quantity;
      
      $invoice->update($input);

      return response()->json([ 'invoice'=>$invoice ],200);
    
    }

 


    public function invoicesByUser($id, Request $request)
    {
      $invoicesbyuser = DB::table('invoices')
       ->where('invoices.CustomerID',$id)
       ->groupBy('InvoiceID')
    //   ->orderBy("start_at", "desc")
       ->get();
       return response()->json($invoicesbyuser, 200);

    }


    public function itemsByInvoice($id, Request $request)
    {
      $items = DB::table('appointements')
       ->where('idinvoice',$id)
    //->orderBy("start_at", "desc")
       ->get();
       return response()->json($items, 200);

    }




    public function addItemInvoice(Request $request)
    {
        $input = $request->all();
        $invoice = Invoices::create($input);
        return response()->json($invoice, 201);
    }


}



