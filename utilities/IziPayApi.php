<?php
require "../WebservicesCredentials.php";
class IziPayApi
{
    public $authentication;
    public $urlIziPay;

    public function __construct()
    {
        $instance = new WebservicesCredentials();
        $this->authentication = $instance->getHeaderBasicIziPay();
        $this->urlIziPay = $instance->IZI_PAY_URL;
    }

    public function isValidTransactionByUuid($uuid): bool
    {
        try {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "$this->urlIziPay/api-payment/V4/Transaction/Get",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
                "uuid": "' . $uuid . '"
                }',
                CURLOPT_HTTPHEADER => array(
                    "Authorization: $this->authentication",
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $response = json_decode($response, true);
            if ($response["status"] == "SUCCESS") {
                $transactionStatus = $response["answer"]["transactionDetails"]["cardDetails"]["authorizationResponse"]["authorizationResult"];
                if (intval($transactionStatus) == 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
            return $response;
        } catch (\Throwable $th) {
            echo $th;
            return false;
        }
    }
}
