<?php

namespace App\Http\Controllers;

use App\donator;
use App\Payment;
use Illuminate\Http\Request;
use Omnipay\Omnipay;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return abort(404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->input('stripeToken')) {
  
            $gateway = Omnipay::create('Stripe');
            $gateway->setApiKey(env('STRIPE_SECRET_KEY'));
          
            $token = $request->input('stripeToken');
          
            $response = $gateway->purchase([
                'amount' => $request->input('amount'),
                'currency' => env('STRIPE_CURRENCY'),
                'token' => $token,
                'description'=> 'This is a test purchase transaction.',
                'receipt_email' => $request->input('email'),
            ])->send();
          
            if ($response->isSuccessful()) {
                // payment was successful: insert transaction data into the database
                $arr_payment_data = $response->getData();
                 
                $isPaymentExist = Payment::where('payment_id', $arr_payment_data['id'])->first();
          
                if(!$isPaymentExist)
                {
                    $payment = new Payment;
                    $donator = donator::find($request->donator_id);

                    $payment->donator_id= $request->donator_id;
                    $payment->donation_type= $request->donaton_type;
                    $payment->payment_id = $arr_payment_data['id'];
                    $payment->payer_email = $request->input('email');
                    $payment->amount = $arr_payment_data['amount']/100;
                    $payment->currency = env('STRIPE_CURRENCY');
                    $payment->payment_status = $arr_payment_data['status'];

                    $donator->first_name = $request->first_name;
                    $donator->last_name = $request->last_name;
                    $donator->home_phone = $request->home_phone;
                    $donator->address = $request->address;
                    $donator->email = $request->email;

                    $donator->save();
                    $payment->save();
                }
 
                return "Payment is successful. Your payment id is: ". $arr_payment_data['id'];
            } else {
                // payment failed: display message to customer
                return "..." . $response->getMessage();
            }
        }
    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return abort(404);
    }
    
    public function checkData(Request $request){
        // return $request;
        $donationData = $request;
        $donator = donator::where('cel_phone',$request->phone)->first();
        if(is_null($donator)){
            $donator= new donator;
            $donator->cel_phone= $request->phone;
            $donator->save();
        }
        return view('donation.checkout',compact('donator','donationData'));

    }
}
