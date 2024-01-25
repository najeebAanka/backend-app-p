<?php 

define ('HMAC_SHA256', 'sha256');
define ('SECRET_KEY', '8d534ad41a154a4bbefc4b637e72a99d210907e275bb4a6aaddecceb17239c6a9c98e2db17c241b09b5ba415ca0f151330b1d5073e7749d7b437c77cae674d1e107712262efa47879fef3143739734086eae584f7d2e48c4867c157fec97c4880fd122ff27334b3b8d88301b145ffe17bd550260c9da4c6d8a58a53fe8c442d2');

function sign ($params) {
  return signData(buildDataToSign($params), SECRET_KEY);
}

function signData($data, $secretKey) {
    return base64_encode(hash_hmac('sha256', $data, $secretKey, true));
}

function buildDataToSign($params) {
        $signedFieldNames = explode(",",$params["signed_field_names"]);
        foreach ($signedFieldNames as $field) {
           $dataToSign[] = $field . "=" . $params[$field];
        }
        return commaSeparate($dataToSign);
}

function commaSeparate ($dataToSign) {
    return implode(",",$dataToSign);
}

?>

<html>
<head>
    <title>test wallet - charging</title>
    <link rel="stylesheet" type="text/css" href="{{url('payment-form')}}/payment.css"/>
      <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    
    
    
<form id="payment_confirmation" action="https://secureacceptance.cybersource.com/pay" method="post"/>
 
<?php
    foreach($_REQUEST as $name => $value) {
        $params[$name] = $value;
    }
?>

    <?php
        foreach($params as $name => $value) {
            echo "<input type=\"hidden\" id=\"" . $name . "\" name=\"" . $name . "\" value=\"" . $value . "\"/>\n";
        }
        echo "<input type=\"hidden\" id=\"signature\" name=\"signature\" value=\"" . sign($params) . "\"/>\n";
    ?>
<p>Processing..</p>
<p>Please wait..</p>

</form>
    
    
 
    
    
    
    
    
    
    <script>
    
 document.getElementById('payment_confirmation').submit();
    </script>
</body>
</body>
</html>
