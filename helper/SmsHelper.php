<?php
session_start();
require_once ("../helper/CurlHelper.php");
require_once ("../helper/AuthHelper.php");
require_once ("../helper/SmsTemplate.php");

//add helper to handle curl requests
use helper\CurlHelper;
use helper\SmsTemplate;

class SmsHelper extends AuthHelper {
    
    /** @var $apiToken oAuth token return by auth api */
    public $apiToken;
    
    /** @var $url url for sms send api */
    public $url;
    
    /** @var $to mobile number on which sms wanted to send */
    public $to;
    
    /** @var $from sender of sms */
    public $from;
            
    function __construct() {
        // initialize apiToken and set it in session if token expires time breach
        
        $currentTimestamp = time();
        if((empty($_SESSION['tokenTime']) && empty($_SESSION['expiresIn'])) || ($currentTimestamp - $_SESSION['tokenTime']) > $_SESSION['expiresIn']) {
            $tokenObj = $this->genToken();
            if(!empty($tokenObj)) {
                $tokenData = json_decode($tokenObj, true);
                $this->apiToken = $tokenData['access_token'];
                $_SESSION['token'] = $this->apiToken;
                $_SESSION['expiresIn'] = $tokenData['expires_in'];
                $_SESSION['tokenTime'] = $currentTimestamp;
            }
        } else {
            /** set apiToken from session */
            $this->apiToken = $_SESSION['token'];
        }
        $this->url = "https://connect.routee.net/sms"; //end point for sms api
        $this->to = '+306978745957';
        $this->from = 'amdTelecom';
    }
    
    /**
     * method to send SMS
     * @return json
     * @throws \RuntimeException
     */
    function sendSms() {
        $resp = [];
        try {
            /** check whether temperature is present or not */
            if(isset($_SESSION['temperature'])) { 
                
                /** initiate sms template helper */
                $smsTemplateObj = new SmsTemplate($_SESSION['temperature']);
                
                /** get sms template */
                $smsTemplate = $smsTemplateObj->getSmsTemplate();
                
                /** set options to curl call */
                if(!empty($smsTemplate)) {
                    $postData = [
                        'body' => $smsTemplate,
                        'to' => $this->to,
                        'from' => $this->from,
                    ];
                    $postData = json_encode($postData);
                    $curlOptionArr = [
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => $postData,
                        CURLOPT_HTTPHEADER => array(
                          "authorization: Bearer $this->apiToken",
                          "content-type: application/json"
                        ),
                    ];
                    $curlres = new CurlHelper($this->url, $curlOptionArr);
                    $resp = $curlres->getResponse();
                } else {
                    throw new \RuntimeException('Issue with sending sms please try later');
                }
            } else {
                throw new \RuntimeException('Temperature data not found');
            }
        } catch (\Exception $ex) {
            $resp['error'] = $ex->getMessage();
            $resp = json_encode($resp);
        }
        return $resp;
    }
}