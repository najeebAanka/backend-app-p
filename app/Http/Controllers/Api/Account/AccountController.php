<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favourite;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\AuctionController;
use stdClass;
use App\Models\User;
use App\Models\WalletRechargeRecord;
use App\Http\Controllers\Helpers\PaymentUtils;

class AccountController extends Controller {

    //





    public function getWonLots(Request $request) {
        $u = Auth::user();
        $response = [];
       
        $data = \App\Models\AuctionHorseReg::where('status_string', 'sold')
                    ->where('winner_id', $u->id)->orderBy('id', 'desc')->paginate(50);
        foreach ($data->getCollection() as $d) {
             $horse = \App\Models\Horse::find($d->horse_id);
            $auc = \App\Models\Auction::find($d->auction_id);
            $obj = new \stdClass();
            $obj->auction = $auc->name . " (" . $d->lot_type . ")";
            $obj->lot = $d->order_sn + 1;
            $obj->finished = $d->lot_end_date != "" ? \Carbon\Carbon::parse($d->lot_end_date)->format('d/m/y h:i') : "Not set";
            $obj->selling = $d->target_type == 'horse' ? "Actual horse" : ( $horse->gender != 'mare' ? "Breeding right" : "Embryo" );
            $obj->amount = number_format($d->current_bid, 0) . " " . $auc->currency;
            $obj->status = "Waiting for payment";
            $invoice_rec = \App\Models\LotWinningRecord::where('lot_id' ,$d->id)->first()
                    ;
               $obj->invoice_url = "https://secure.test.com/remote-operations/invoices/view/-1";
            if($invoice_rec){
              if($invoice_rec->invoice_id != -1){
            $obj->invoice_url = "https://secure.test.com/remote-operations/invoices/view/".$invoice_rec->invoice_id;
              }  
            }
            
            $response[] = $obj;
        }
        return $this->formResponse("Retrieved", $response, 200);
    }

    
    
        public function sendRefundRequest(Request $request, $id) {
        $record = WalletRechargeRecord::find($id);
        if ($record && $record->order_status == 'paid') {
           $record->order_status = 'refund-requested';
           $record->save();
            return $this->formResponse("Refund request recieved , it will be processed soon", null, 200);
        } else {
            return $this->formResponse("Record is not refundable or does not exist !", null, 404);
        }
    }
    
    public function getMyWallet() {
        $u = Auth::user();
        $w = new stdClass();
        $w->holder = $u->name;
        $w->news = "Wallet amount is in AED (Arab emirates dirham) will be automatically converted to Auction currency when checking deposit . exchange rate is : 3.65";
        $w->balance = $u->wallet_amount;
        $w->currency = 'AED';
        $w->balance_formatted = number_format($u->wallet_amount, 0);
        $wallet_charge_buttons = [ 100 ,10000 ,16000 ,20000 ,50000 ,75000 ,100000];
        $w->wallet_charge_buttons = $wallet_charge_buttons;
        $history = [];
        foreach (\App\Models\WalletRechargeRecord::where('user_id', $u->id)->orderBy('id', 'desc')->take(25)->get() as $rec) {
            $p = new \stdClass();
            $p->id = $rec->id;
            $p->amount = $rec->amount." AED";
            $p->date = \Carbon\Carbon::parse($rec->created_at)->format('d/m/Y h:i a');
            $p->status = "Unknown";
            
            if($rec->order_status  == 'paid') $p->status ="Success";
            if($rec->order_status  == 'refund-requested') $p->status = 'Refund requested';
            if($rec->order_status  == 'created') $p->status = 'Unpaid';
            if($rec->order_status  == 'refunded') $p->status = 'Refunded';
            
            $history[] = $p;
        }
        $w->recharge_history = $history;
        return $this->formResponse("Wallet receieved", $w, 200);
    }

    public function paymentCallback(Request $request) {
     $id = $request->req_reference_number;
      $record = WalletRechargeRecord::find($id);
        $user = User::find($record->user_id);
    if ($record && $record->order_status == 'created') {
            //call check paymnet appi and see if order is paid
          
            if($request->reason_code == "100"){
                  $record->order_status = 'paid';
          
            $user->wallet_amount += $record->amount;
            $user->save();
            
          
            
            }else{
               $record->order_status = 'unpaid';
            }
            $record->order_ref = $request->transaction_id;
            $record->order_response = json_encode( $request->all());
            $record->save();   
              if($user && $user->is_email_verified == 1  && $record->order_status == 'paid'){
                    \Illuminate\Support\Facades\Mail::to($user->email)
                ->send(new \App\Mail\WalletRechargeInvoiceTemplate($record));
            }
            return \Illuminate\Support\Facades\Redirect::to('https://test.com/profile?active=wallet');
        } else {
            return $this->formResponse("Record is no longer valid !", null, 404);
        }
    }

    public function payRechargeCallback(Request $request, $code) {


        $record = WalletRechargeRecord::where('gen_code', $code)->first();

        if ($record && $record->order_status == 'created') {

            //call check paymnet appi and see if order is paid

            $record->order_status = 'paid';
            $record->save();
            $user = User::find($record->user_id);
            $user->wallet_amount += $record->amount;
            $user->save();

            return view('payment.payment-success')->with(['amount' => $user->wallet_amount]);
        } else {
            return $this->formResponse("Record is no longer valid !", null, 404);
        }
    }

    public function payRechargeRequest(Request $request, $code) {


        $record = WalletRechargeRecord::where('gen_code', $code)->first();
        //create order here and redirect to payment page

        if ($record && $record->order_status == 'created') {
            return view('payment.payment_form')->with(['record' => $record]);
        } else {
            return $this->formResponse("Record is no longer valid !", null, 404);
        }
    }

    public function rechargeWallet(Request $request) {


        $validator = Validator::make($request->all(), [
                    'amount' => 'required',
                    'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->formResponse($this->failedValidation($validator), null, 400);
        }

        $record = new WalletRechargeRecord();
        $record->user_id = Auth::id();
        $record->payment_method = $request->payment_method;
        $record->amount = $request->amount;
        $record->gen_code = "test";
        $record->save();
        $record->gen_code = md5($record->id . $record->creatd_at);
        $record->save();
        return $this->formResponse("Redirecting to payment link..", url('api/v1/operations/recharge-wallet-requests/pay/' . $record->gen_code), 200);
    }

    private function getFavouriteObject($target_type, $target_id, $controller) {
        $object = new stdClass();
        if ($target_type == 1) {
            if (\App\Models\Auction::find($target_id))
                $object = $controller->getAuctionByIdInternal(request(), $target_id);
            else
                return null;
        }

        if ($target_type == 2) {
            if (\App\Models\AuctionHorseReg::find($target_id))
                $object = $controller->getLotByIdInternal(request(), $target_id);
            else
                return null;
        }
        $object->in_favourite = true;
        return $object;
    }

    public function getFavourites(Request $request) {
        $me = Auth::id();
        $data = \App\Models\Favourite::where('user_id', $me);
        if ($request->has('target_type')) {
            $data = $data->where('target_type', $request->taregt_type);
        }
        $controller = new AuctionController();
        $data = $data->get();
        $list = [];
        foreach ($data as $d) {
            $d->object = $this->getFavouriteObject($d->target_type, $d->target_id, $controller);
            if ($d->object)
                $list[] = $d;
        }


        return $this->formResponse("Items rerieved", $list, 200);
    }

    public function handleFavourite(Request $request) {

        $validator = Validator::make($request->all(), [
                    'target_type' => 'required',
                    'target_id' => 'required',
                    'action' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->formResponse($this->failedValidation($validator), null, 400);
        }

        $me = Auth::id();
        $target_type = $request->target_type;
        $target_id = $request->target_id;
        $row = \App\Models\Favourite::where('user_id', $me)->where('target_id', $target_id)
                        ->where('target_type', $target_type)->first();

        if ($row && $request->action == 'add') {
            return $this->formResponse("Already added to favourites", null, 400);
        }
        if (!$row && $request->action == 'remove') {
            return $this->formResponse("No records found to remove", null, 400);
        }


//auth::user()->sendNotification('added-to-favourites', $target_type, "an item was added to your favourite !",null);

        if ($request->action == 'add') {
            $f = new Favourite();
            $f->target_type = $target_type;
            $f->target_id = $target_id;
            $f->user_id = $me;
            $f->save();
            
              $update = new \App\Models\ActivityTracker();
             $update->target_id =  $me ;
        $update->target_type = "fav-add";
        $update->contents =   Auth::user()->name." added an item to thier favourite";
        $update->save();
            
            return $this->formResponse("Added to favourites", null, 200);
        }
        if ($request->action == 'remove') {
            $row->delete();
            return $this->formResponse("Removed from favourites", null, 200);
        }
    }

    public function verifyEmail(Request $request, $otp) {
        $u = User::where('email_otp', $otp)->first();
        if ($u) {
            $u->email_otp = "";
            $u->is_email_verified = 1;
            $u->save();
            $controller = new \App\Http\Controllers\Api\AuthController();
            $controller->sendWelcomeEmail($u);
           
                $update = new \App\Models\ActivityTracker();
        $update->target_id =  $u->id ;
        $update->target_type = "email-verified";
        $update->contents =   $u->name." verified thier email";
        $update->save();
        
        
        
            
            return view('dashboard.pages.email-landing-page')->with(['title' => 'Email is verified', 'details' => 'Your email is verified , now you can enjoy using test.com and recieve updates on your email.']);
         
        } else {
            return view('dashboard.pages.email-landing-page')->with(['title' => 'Link is expired', 'details' => '<p>'
                        . 'This link you are trying to use is invalid or expired , you can contact us for any inconvinience</p>']);
        }
    }

    public function resetPassword(Request $request, $otp) {
        $u = User::where('password_resest_req_code', $otp)->first();
        if ($u) {
            $u->password_resest_req_code = "";
            $password = rand(10000000, 99999999);
            $u->password = bcrypt($password);
            $u->is_email_verified = 1;
            $u->save();

            return view('dashboard.pages.email-landing-page')->with(['title' => 'Password is reset'
                        , 'details' => 'Your temporary password now is <br />' . $password . '</br >Please change it soon.']);
        } else {
            return view('dashboard.pages.email-landing-page')->with(['title' => 'Link is expired', 'details' => '<p>'
                        . 'This link you are trying to use is invalid or expired , you can contact us for any inconvinience</p>']);
        }
    }

}
