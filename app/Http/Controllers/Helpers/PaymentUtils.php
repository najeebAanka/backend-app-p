<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Http\Controllers\Helpers;

define ('HMAC_SHA256', 'sha256');
define ('SECRET_KEY', '');

/**
 * Description of PaymentUtils
 *
 * @author braainclick
 */
class PaymentUtils {
    //put your code here


public static function sign ($params) {
  return self::signData(self::buildDataToSign($params), SECRET_KEY);
}

private static function signData($data, $secretKey) {
    return base64_encode(hash_hmac('sha256', $data, $secretKey, true));
}

private static function buildDataToSign($params) {
        $signedFieldNames = explode(",",$params["signed_field_names"]);
        foreach ($signedFieldNames as $field) {
           $dataToSign[] = $field . "=" . $params[$field];
        }
        return self::commaSeparate($dataToSign);
}

private static function commaSeparate ($dataToSign) {
    return implode(",",$dataToSign);
}

}
?>
