<?php

namespace App\Services\ESMS;

use App\Services\Service;

class ESMSService extends Service
{
    use ESMSMessageMap;
    protected $apiKey = '';
    protected $secretKey = '';

    protected $errorMessage = '';

    public function __construct($config = [])
    {
        $this->config($config);
    }

    public function config($config = [])
    {
        $this->apiKey = $config['apiKey'] ?? ($config['api_key'] ?? config('esms.apikey'));
        $this->secretKey = $config['secretKey'] ?? ($config['secret_key'] ?? config('esms.secretkey'));
    }

    public function getErrorMessage() : string {
        return $this->errorMessage;
    }

    public function sendMessage($phone, $message = '')
    {
        $this->errorMessage = '';
        try {

            $APIKey = $this->apiKey;
            $SecretKey = $this->secretKey;
            $YourPhone = $phone;
            $ch = curl_init();

            $SampleXml = "<RQST>"
                . "<APIKEY>" . $APIKey . "</APIKEY>"
                . "<SECRETKEY>" . $SecretKey . "</SECRETKEY>"
                . "<ISFLASH>0</ISFLASH>"
                . "<SMSTYPE>2</SMSTYPE>"
                . "<CONTENT>" . $message . "</CONTENT>"
                . "<BRANDNAME>Baotrixemay</BRANDNAME>" //De dang ky brandname rieng vui long lien he hotline 0902435340 hoac nhan vien kinh Doanh cua ban
                . "<CONTACTS>"
                . "<CUSTOMER>"
                . "<PHONE>" . $YourPhone . "</PHONE>"
                . "</CUSTOMER>"
                . "</CONTACTS>"
                . "</RQST>";


            curl_setopt($ch, CURLOPT_URL,            "http://api.esms.vn/MainService.svc/xml/SendMultipleMessage_V4/");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST,           1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,     $SampleXml);
            curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain'));

            $result = curl_exec($ch);
            $xml = simplexml_load_string($result);

            if ($xml === false) {
                $this->errorMessage = 'Error parsing XML';
                return false;
            }

            //now we can loop through the xml structure
            //Tham khao them ve SMSTYPE de gui tin nhan hien thi ten cong ty hay gui bang dau so 8755... tai day :http://esms.vn/SMSApi/ApiSendSMSNormal

            if($xml->CodeResult == 100){
                return true;
            }
            $this->errorMessage = $this->getMessage($xml->CodeResult . '');
            return false;



        } catch (\Throwable $th) {
            $this->errorMessage = $th->getMessage();
            return false;
        }
    }

    public function sendVerifyCode($phone, $code) : bool {
        return $this->sendMessage($phone, $code. ' la ma xac minh dang ky Baotrixemay cua ban');
    }
}
