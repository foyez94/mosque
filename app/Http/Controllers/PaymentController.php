<?php

namespace App\Http\Controllers;

use App\donation;
use App\donationDaily;
use App\donationMonthly;
use App\donator;
use App\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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
       
       return view('donation.test.index');

    }
    public function testCreateSession(Request $request){
     
        $donation = new donation;
        
$donation->save();
       
        \Stripe\Stripe::setApiKey('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');
        
        header('Content-Type: application/json');
        
        
        $checkout_session = \Stripe\Checkout\Session::create([
        
          'payment_method_types' => ['card'],
        
          'line_items' => [[
        
            'price_data' => [
        
              'currency' => 'usd',
        
              'unit_amount' => $request->amount*100 ,
        
              'product_data' => [
        
                'name' => 'Donation for MMC'." - ".$request->donation_type,
        
                'images' => [ "https://masjidmissioncenterusa.org/abasas/images/logo/MMC_Title_logo-removebg-preview.png" ],
        
              ],
        
            ],
        
            'quantity' => 1,
        
          ]],
        
          'mode' => 'payment',
        
          'success_url' => route('donationSuccess',['donation'=>$donation->id]),
        
          'cancel_url' => route('donationFailed'),
        
        ]);


$donation->first_name = $request->first_name;
$donation->last_name = $request->last_name;
$donation->address = $request->address;
$donation->home_phone = $request->home_phone;
$donation->cell_phone = $request->cell_phone;
$donation->donation_type = $request->donation_type;
$donation->amount = $request->amount;
$donation->session = $checkout_session->id;
$donation->save();


        
        return ['id' => $checkout_session->id];




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
            $gateway->setApiKey('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');
          
            $token = $request->input('stripeToken');
          
            $response = $gateway->purchase([
                'amount' => $request->input('amount'),
                'currency' => env('STRIPE_CURRENCY'),
                'token' => $token,
                'description'=> 'Donation for MMC. Donation type : '. $request->donaton_type ,
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
                    $this->addDailyMonthly($payment->amount);
                }
                $paymentData = array("amount"=>$payment->amount, "email"=>$payment->payer_email, "payment_id"=>$payment->payment_id);
                
                return redirect(route('donationSuccess',$paymentData)) ;
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



    public function donationIndex()
    {
        return view('donation.index');
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
         return "Under Construction";
        $donationData = $request;
        $donator = donator::where('cel_phone',$request->phone)->first();
        if(is_null($donator)){
            $donator= new donator;
            $donator->cel_phone= $request->phone;
            $donator->save();
        }
        return view('donation.checkout',compact('donator','donationData'));

    }
    public function addDailyMonthly($amount){
        $date = Carbon::now()->format('Y-m-d');
        $month = Carbon::now()->format('Y-m-01');
        $dailyDonation = donationDaily::where('date',$date)->first();
        $monthlyDonation = donationMonthly::where('month',$month)->first();
        if(is_null($dailyDonation)){
            $dailyDonation =new donationDaily;
            $dailyDonation->date= $date;
        }
        if(is_null($monthlyDonation)){
            $monthlyDonation =new donationMonthly;
            $monthlyDonation->month= $month;
        }
        $dailyDonation->amount+= $amount;
        $monthlyDonation->amount+= $amount;
        
        $dailyDonation->save();
        $monthlyDonation->save();

    }
    public function success(Request $request){
        $donation = donation::find($request->donation);
        

        $stripe = new \Stripe\StripeClient(
            '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'
          );
       $currentSession=    $stripe->checkout->sessions->retrieve(
        $donation->session,
            []
          );
           $currentSession;
        $customer=  $stripe->customers->retrieve(
            $currentSession->customer,
            []
          );


          $email = $customer->email;



          $donation->payer_email= $email;
          $donation->payment_status= 'success';
          $donation->save();

       
         $this->addDailyMonthly($donation->amount);
        // $payment = new Payment;
        // $donator = new donator;

        // $payment->donator_id= $request->donator_id;
        // $payment->donation_type= $request->donaton_type;
        // $payment->payment_id = $arr_payment_data['id'];
        // $payment->payer_email = $request->input('email');
        // $payment->amount = $arr_payment_data['amount']/100;
        // $payment->currency = env('STRIPE_CURRENCY');
        // $payment->payment_status = $arr_payment_data['status'];

        // $donator->first_name = $request->first_name;
        // $donator->last_name = $request->last_name;
        // $donator->home_phone = $request->home_phone;
        // $donator->address = $request->address;
        // $donator->email = $request->email;

        // $donator->save();
        // $payment->save();
        return view('donation.success',compact('donation')) ;
    }
    public function failed(){
        return view('donation.failed') ;
    }
}
