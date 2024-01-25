<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Auction;
use App\Models\User;
use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
class InvoicesController extends Controller {

    
    
    public function refundRechargeWalletRequest(Request $request, $id){
        $r = \App\Models\WalletRechargeRecord::find($id);
        $r->order_status = 'refunded';
        $r->save();
        $u = User::find($r->user_id);
        if($u){
            $u->wallet_amount-=$r->amount;
            $u->save();
            $u->sendNotification('wallet', 'wallet', "Amount ",$r->amount." AED is now refunded back to your account", null);
        }
        return back()->with('message' ,'Transaction set as refunded');
    }
    public function generateRefundForm(Request $request, $id){
        
     //   dd(sys_get_temp_dir());
        
        
        $r = \App\Models\WalletRechargeRecord::find($id);
        
      if($r->order_response != "") { 
          $data = json_decode($r->order_response);
          $card = $data->req_card_number;
         
        $u = User::find($r->user_id);
        
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
  $section = $phpWord->addSection();
  $header = $section->addHeader();
  $header->addImage("./dashboard/assets/img/dib.png" ,['width'=>400 ,
'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);  
  
  
  $html = '<h1 style="text-align: center;"><strong><u>REFUND FORM</u></strong></h1>
<p style="text-align: center;">&nbsp;</p>
<table style="width: 570px; margin-left: auto; margin-right: auto;">
<tbody>
<tr>
<td style="width: 554px; padding: 5px;" colspan="2">
<p><strong>To: Dubai Islamic Bank</strong></p>
</td>
</tr>
<tr>
<td style="width: 266px; padding: 5px;"><strong>Request Date: </strong></td>
<td style="width: 288px; padding: 5px; text-align: center;">'.date('d/F/Y').'&nbsp;</td>
</tr>
<tr>
<td style="width: 266px; padding: 5px;"><strong>Merchant ID: </strong></td>
<td style="width: 288px; padding: 5px; text-align: center;">000170289&nbsp;</td>
</tr>
<tr>
<td style="width: 266px; padding: 5px;"><strong>Merchant Name :</strong></td>
<td style="width: 288px; padding: 5px; text-align: center;">Dubai Arabian Horse Stud&nbsp;</td>
</tr>
<tr>
<td style="width: 554px; padding: 5px;" colspan="2"><strong><br /></strong><span style="text-decoration: underline;">Refund details:</span><strong><br /><br /></strong></td>
</tr>
<tr>
<td style="width: 266px; padding: 5px;" colspan="2"><strong>Card Holder Number:</strong></td>
</tr>
<tr>
<td style="width: 266px; padding: 5px;" colspan="2">&nbsp;
<table style="height: 19px; margin-bottpm: 20px;" width="553">
<tbody>
<tr>
<td style="width: 32.7188px; text-align: center; padding: 2px; border: 1px solid black;">x</td>
<td style="width: 32.7188px; text-align: center; padding: 2px; border: 1px solid black;">x</td>
<td style="width: 32.7188px; text-align: center; padding: 2px; border: 1px solid black;">x</td>
<td style="width: 32.7188px; text-align: center; padding: 2px; border: 1px solid black;">x</td>
<td style="width: 32.7188px; text-align: center; padding: 2px; border: 1px solid black;">x</td>
<td style="width: 32.7188px; text-align: center; padding: 2px; border: 1px solid black;">x</td>
<td style="width: 32.7188px; text-align: center; padding: 2px; border: 1px solid black;">x</td>
<td style="width: 32.7188px; text-align: center; padding: 2px; border: 1px solid black;">x</td>
<td style="width: 32.7188px; text-align: center; padding: 2px; border: 1px solid black;">x</td>
<td style="width: 32.7188px; text-align: center; padding: 2px; border: 1px solid black;">x</td>
<td style="width: 32.7188px; text-align: center; padding: 2px; border: 1px solid black;">x</td>
<td style="width: 32.7188px; text-align: center; padding: 2px; border: 1px solid black;">x</td>
<td style="width: 32.7188px; text-align: center; padding: 2px; border: 1px solid black;">'.$card[12].'</td>
<td style="width: 32.7188px; text-align: center; padding: 2px; border: 1px solid black;">'.$card[13].'</td>
<td style="width: 32.7188px; text-align: center; padding: 2px; border: 1px solid black;">'.$card[14].'</td>
<td style="width: 32.7188px; text-align: center; padding: 2px; border: 1px solid black;">'.$card[15].'</td>
</tr>
</tbody>
</table>
<br /><br />
</td>
</tr>
<tr>
<td style="width: 266px; padding: 5px;">Transaction Date</td>
<td style="width: 288px; padding: 5px; text-align: center;">'. Carbon::parse($r->created_at)->format('d/F/Y h:m: a').'&nbsp;</td>
</tr>
<tr>
<td style="width: 266px; padding: 5px;">Auth / Approval Code</td>
<td style="width: 288px; padding: 5px; text-align: center;">'.$data->auth_code.'&nbsp;</td>
</tr>
<tr>
<td style="width: 266px; padding: 5px;">Merchant Reference No</td>
<td style="width: 288px; padding: 5px; text-align: center;">'.$data->req_reference_number.'&nbsp;</td>
</tr>
<tr>
<td style="width: 266px; padding: 5px;">Cybersource Reference No&nbsp;</td>
<td style="width: 288px; padding: 5px; text-align: center;">'.$data->transaction_id.'&nbsp;</td>
</tr>
<tr>
<td style="width: 266px; padding: 5px;">Refund Type</td>
<td style="width: 288px; padding: 5px; text-align: center;">FULL</td>
</tr>
<tr>
<td style="width: 266px; padding: 5px;">Transaction Amount</td>
<td style="width: 288px; padding: 5px; text-align: center;">'.$data->auth_amount.' '.$data->req_currency.'&nbsp;</td>
</tr>
<tr>
<td style="width: 266px; padding: 5px;">Refund Amount&nbsp;</td>
<td style="width: 288px; padding: 5px; text-align: center;">'.$data->auth_amount.' '.$data->req_currency.'&nbsp;</td>
</tr>
<tr>
<td style="width: 266px; padding: 5px;">Reason for Refund&nbsp;</td>
<td style="width: 288px; padding: 5px; text-align: center;">test / DAHC Refund policy&nbsp;</td>
</tr>
<tr>
<td style="width: 554px; padding: 5px;" colspan="2"><br /><strong>Card Holder&rsquo;s Contact Details:</strong><br /><br /></td>
</tr>
<tr>
<td style="width: 266px; padding: 5px;">Name</td>
<td style="width: 288px; padding: 5px; text-align: center;">'.$data->req_bill_to_forename.' '.$data->req_bill_to_surname.'&nbsp;</td>
</tr>
<tr>
<td style="width: 266px; padding: 5px;">Mobile/Telephone</td>
<td style="width: 288px; padding: 5px; text-align: center;">'.(isset($data->req_bill_to_phone) ? $data->req_bill_to_phone :$data-> req_bill_to_email).'&nbsp;</td>
</tr>
<tr>
<td style="width: 266px; padding: 5px;"><br /><br /><strong>Authorized Signature</strong></td>
<td style="width: 288px; padding: 5px; text-align: right;"><br /><br /><strong>Company Seal</strong></td>
</tr>
<tr>
<td style="width: 266px; padding: 5px;">&nbsp;</td>
<td style="width: 288px; padding: 5px; text-align: center;">&nbsp;</td>
</tr>
</tbody>
</table>';

\PhpOffice\PhpWord\Shared\Html::addHtml($section, $html, false, false);

  
  
//  $section->addText("REFUND FORM",['name'=>'Times New Roman','size' => 20,'bold' => true ,'underline' => true ,
//      'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]); 
  // Simple text
//$section->addTitle('REFUND FORM', 1);
  
// 
//         $section->addImage("./dashboard/assets/img/dib.png" ,array('width'=>550 ,
//             'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));  
//          $section = $phpWord->addSection();
//        $text = $section->addText("Hello this is a test",array('name'=>'Arial','size' => 20,'bold' => true));
        $path = Storage::path('public/docs/Refund-Request-Form-'.$data->transaction_id.'.docx');    
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        try{
            \PhpOffice\PhpWord\Settings::setTempDir(Storage::path('public/docs/Temp'));  
        $objWriter->save($path);
          return response()->download($path);
        }catch(Exception $e){
             return back()->with('error' ,'Server does not give permission to create word file');  
        }
       
      
      }else{
              return back()->with('error' ,'Payemnt was not completed');
      }
    }
    
    public function emailInvoice(Request $request, $invoice_id){
        $r = \App\Models\Invoice::find($invoice_id);
       $email = $request->email;
     //  return view('emails.lots-won')->with('data' ,json_decode($r->contents));
        if($r){
        
            
            \Illuminate\Support\Facades\Mail::to($email)
                ->send(new \App\Mail\LotsWonInvoiceTemplate($r));  
            
            
            $h = new \App\Models\InvoiceEmailHistory();
            $h->inv_id = $invoice_id;
            $h->email =  $email ;
            $h->save();  
         
        }
        return back()->with('message' ,'Invoice was emailed succesfully');
    }
    
    
    public function showInvoice(Request $request, $id) {
        if($id != -1){
        $invoice = Invoice::find($id);
        return view('invoices.' . $invoice->invoice_type)->with(["data" => json_decode($invoice->contents)]);
        }else{
         echo "No invoices generated yet !";   
        }
    }

    public function generateLotsWonInvoice(Request $request, $user_id, $user_type, $auction_id) {
        $target_key = $user_id . "-" . $auction_id;
        $invoice = Invoice::where('target_key', $target_key)->first();
        if (!$invoice) {
            $invoice = new Invoice();
            $invoice->target_key = $target_key;
            $invoice->invoice_type = 'lots-won';
            $invoice->gen_id = "INV-LW-" . (\App\Models\Invoice::count() + 8762);
            $invoice->user_id = $user_id;
            $invoice->user_type = $user_type;
            $invoice->created_by = Auth::id();
            $invoice->auction_id = $auction_id;
        }
        $data = new \stdClass();
        $data->invoice_no = $invoice->gen_id;
        $data->invoice_date = Carbon::now()->format('d.m.Y');
        $data->invoice_to = "";
        if ($user_type == 'user') {
            $u = User::find($user_id);
            $data->invoice_to = $u->name;
            $data->invoice_to .= '<br />';
            $data->invoice_to .= $u->country;
            $data->invoice_to .= '<br />';
            $data->invoice_to .= $u->phone;
            $data->invoice_to .= '<br />';
            $data->invoice_to .= $u->email;
        } else {

            $data->invoice_to = "Hall bidder No(" . $user_id . ")";
        }
        $data->data = [];

        $all_mixed = \App\Models\LotWinningRecord::where('auction_id', $auction_id)
                        ->where('winner_id', $user_id)->where('winner_type', $user_type)->orderBy('id')->get();
        $subtotal = 0;
        $auction = Auction::find($auction_id);
        foreach ($all_mixed as $a) {
            $horse = \App\Models\Horse::find($a->horse_id);
            $lot = \App\Models\AuctionHorseReg::find($a->lot_id);
            $row = new \stdClass();
            $row->item_name = (
                    $a->selling_type == 'breeding-right' ? ($horse->gender == 'mare' ? "Embryo of : " : "Breeding right from : ") : ""

                    ) . $horse->name_en;
            $row->item_description = $auction->name . " (" . "Lot : " . ($lot->order_sn + 1) . ")";
            $row->item_quantity = 1;
            $row->item_price = $a->amount . " " . $a->currency;
            $row->item_total = $a->amount . " " . $a->currency;
            $data->data[] = $row;
            $subtotal += $a->amount;
        }
        $data->subtotal = $subtotal;
        $data->vat = $auction->vat;
        $data->grandtotal = $data->subtotal + ($data->vat * $data->subtotal / 100);
        if ($request->has('exc-deposit')) {
            $data->deposit = $auction->required_deposit;
            $data->grandtotal -= $auction->required_deposit;
        }
        $data->currency = $auction->currency;

        $invoice->contents = json_encode($data);
        $invoice->save();
          foreach ($all_mixed as $a) {
              $a->invoice_id = $invoice->id;
              $a->save();
          }

        return \Illuminate\Support\Facades\Redirect::to('remote-operations/invoices/view/' . $invoice->id);
    }

}
