<?php
class IziPayApi
{
    public $authenticationPrecor;
    public $authenticationMaxco;
    public $urlIziPay;

    public function __construct($iziPayUrl, $usernamePrecor, $passwordPrecor, $usernameMaxco, $passwordMaxco)
    {
        $this->urlIziPay = $iziPayUrl;
        $this->authenticationPrecor = $this->getHeaderBasicIziPay($usernamePrecor, $passwordPrecor);
        $this->authenticationMaxco = $this->getHeaderBasicIziPay($usernameMaxco, $passwordMaxco);
    }

    function getHeaderBasicIziPay(string $username, string $password): string
    {
        return "Basic " . base64_encode($username . ':' . $password);
    }

    public function isValidTransactionByUuid($uuid, $id_soc): bool
    {
        try {
            $basicAuthentication = $this->isMaxco($id_soc) ? $this->authenticationMaxco : $this->authenticationPrecor;
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
                    "Authorization: $basicAuthentication",
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $response = json_decode($response, true);
            if ($response["status"] == "SUCCESS") {
                $transactionStatus = $response["answer"]["transactionDetails"]["cardDetails"]["authorizationResponse"]["authorizationResult"];
                return ($transactionStatus == "0");
            }

            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function isMaxco($id_soc)
    {
        if ($id_soc == "EM01") {
            return true;
        } else if ($id_soc == "MA01") {
            return true;
        } else {
            return false;
        }
    }
}
// IZIPAY

// $IZI_PAY_URL = "https://api.micuentaweb.pe";
// $IZI_PAY_USERNAME = "12158862";
// $IZI_PAY_PASSWORD = "testpassword_o7ct19x6LOUuMNuRsLT8AlzNHbu88p4jHWy7hhsSPRypn";
// $uuid = "3a915bf6982847ce84c1248abaa07362";
// $urlIziPay = new IziPayApi($IZI_PAY_URL, $IZI_PAY_USERNAME, $IZI_PAY_PASSWORD);
// $result = $urlIziPay->isValidTransactionByUuid($uuid);
// print_r($result ? "valido" : "invalido");
