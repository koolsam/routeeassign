<?php
require_once ("../helper/CurlHelper.php");

use helper\CurlHelper;

/**
 * Class to genrate api token
 */

class AuthHelper {
    
    /** @var $appId Application ID for Routee API  */
    private $appId = '5f9138288b71de3617a87cd3';
    
    /** @var $appSecret Application secret for Routee API*/
    private $appSecret = 'CKwG8KXtiO';
    
    /** @var $url oAuth api end point */
    private $url = 'https://auth.routee.net/oauth/token';
    
    /** @var $grantType grant type */
    private $grantType = 'client_credentials';
            
    /**
     * method to get base64 encoded string against app id and app secret
     * @return string
     */
    function getBase64EncodedKey () {
        $key = $this->appId.':'.$this->appSecret;
        $encodedStr = base64_encode($key);
        return $encodedStr;
    }
    
    /**
     * Method to get oAuth token
     * @return json
     * @throws RuntimeException
     */
    function genToken () {
        $resp = [];
        try {
            /** get base64 string for oAuth api authorization  */
            $encodedKey = $this->getBase64EncodedKey();
            
            /** set options for curl call */
            $curlOptionArr = [
                CURLOPT_URL => "https://auth.routee.net/oauth/token",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "grant_type=$this->grantType",
                CURLOPT_HTTPHEADER => array(
                  "authorization: Basic $encodedKey",
                  "content-type: application/x-www-form-urlencoded"
                ),
            ];
            
            $curlres = new CurlHelper($this->url, $curlOptionArr);
            $resp = $curlres->getResponse();
            
        } catch (\Exception $ex) {
            throw new \RuntimeException($ex);
        }
        return $resp;
    }
}

