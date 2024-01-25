<html>
    <head>
        <title>test wallet - charging</title>
        
        <link rel="stylesheet" type="text/css" href="{{url('payment-form')}}/payment.css"/>
        <script type="text/javascript" src="{{url('payment-form')}}/jquery-1.7.min.js"></script>
          <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>









    
    
    


        <form id="payment_form" action="{{url('pc')}}" method="post">
            {{csrf_field()}}
<!--          


 
     
            <input type="hidden" name="signed_field_names" value="access_key,profile_id,transaction_uuid,signed_field_names,unsigned_field_names,signed_date_time,locale,transaction_type,reference_number,amount,currency">
            <input type="hidden" name="unsigned_field_names">

            <input type="hidden" name="locale" value="en">
            <input type="hidden"  name="currency" value="AED" size="25">
            <input type="hidden" name="amount" value="{{(double)$record->amount}}" size="25">
            <input value="{{$record->id}}" type="hidden" name="reference_number" size="25">
            <input type="hidden" value="authorization" name="transaction_type" size="25">
          

-->
            
     <?php $user = \App\Models\User::find($record->user_id); ?>       
            
            <input type="hidden" name="access_key" value="72f12a4aae3a3d089e51d56ec74e8f23">
            <input type="hidden" name="profile_id" value="7B9077FC-2BAB-47D7-B097-ABE69F39938C">
	<input type="hidden" id="transaction_uuid" name="transaction_uuid" value="<?php echo uniqid() ?>"/> 
	<input type="hidden" id="signed_field_names" name="signed_field_names" value="access_key,profile_id,transaction_uuid,signed_field_names,unsigned_field_names,signed_date_time,locale,transaction_type,reference_number,amount,currency,payment_method,bill_to_forename,bill_to_surname,bill_to_email,bill_to_phone,bill_to_address_line1,bill_to_address_city,bill_to_address_state,bill_to_address_country,bill_to_address_postal_code"/> 
	<input type="hidden" name="unsigned_field_names" value="">
	
	<input type="hidden" id="signed_date_time" name="signed_date_time" value="<?php echo gmdate("Y-m-d\TH:i:s\Z"); ?>"/> 
	<input type="hidden" id="locale" name="locale" value="en"/> 
	<input type="hidden" id="transaction_type" name="transaction_type" value="sale"/> 
	<input type="hidden" id="reference_number" name="reference_number" value="{{$record->id}}"/> 
	<input type="hidden" id="amount" name="amount" value="{{(double)$record->amount}}"/> 
	<input type="hidden" id="currency" name="currency" value="AED"/> 
	<input type="hidden" id="payment_method" name="payment_method" value="card"/> 
	<input type="hidden" id="bill_to_forename" name="bill_to_forename" value="{{$user->name}}"/> 
	<input type="hidden" id="bill_to_surname" name="bill_to_surname" value="NAME"/> 
	<input type="hidden" id="bill_to_email" name="bill_to_email" value="{{$user->email}}"/> 

	<input type="hidden" id="bill_to_phone" name="bill_to_phone" value="{{$user->phone}}"/> 
	<input type="hidden" id="bill_to_address_line1" name="bill_to_address_line1" value="1295 Charleston Rd"/> 
	<input type="hidden" id="bill_to_address_city" name="bill_to_address_city" value="Mountain View"/> 
	<input type="hidden" id="bill_to_address_state" name="bill_to_address_state" value="CA"/> 
	<input type="hidden" id="bill_to_address_country" name="bill_to_address_country" value="US"/> 
	<input type="hidden" id="bill_to_address_postal_code" name="bill_to_address_postal_code" value="94043"/> 
            

            <p>Creating request..</p>
            <p>Please wait..</p>
     
        </form>
        
        
        
        
        <script>
document.getElementById('payment_form').submit();
        </script>
    </body>
</html>
