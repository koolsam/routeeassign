<?php
session_start();
require_once ("../helper/CurlHelper.php");

//add helper to handle curl requests
use helper\CurlHelper;

/**
 * Class to get weather data from weather api
 */

class Weather {
    
    /** @var $apiKey api key to call weather api */
    private $apiKey;
    
    /** @var $unit to change weather unit */
    private $unit;
    
    /** @var $lat latitude of current user */
    public $lat;
    
    /** @var lng longitude of current user */
    public $lng;
    
    /** @var $url api endpoint to get weather */
    public $url;
            
    function __construct() {
        
        /** initialize lat lng url and unit */
        if(!empty($_POST['lat']) && !empty($_POST['lng'])) {
            $this->lat = $_POST['lat'];
            $this->lng = $_POST['lng'];
            $this->apiKey = 'b385aa7d4e568152288b3c9f5c2458a5';
            $this->unit  = 'metric'; // metric to get temperature in celsius
            
            /** url to get weather data from lat and lng */
            $this->url = "http://api.openweathermap.org/data/2.5/weather?lat=$this->lat&lon=$this->lng&appid=$this->apiKey&units=$this->unit";
        }
    }

    /**
     * function to get weather data from open weather api
     * @return json
     * @throws \RuntimeException
     */
    public function getWeather() {
        
        /**get current weather from open weather api */
        $resp = [];
        try {
           /** if url is empty then through exception */
            if(!empty($this->url)) {
                
                /** set options to curl call */
                $curlOptionArr = [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 20,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                ];
                
                /** call curl helper to get data from weather api */
                $curlres = new CurlHelper($this->url, $curlOptionArr);
                $resp = $curlres->getResponse();
                
                /** if response is not empty then set temperature in session */
                if(!empty($resp)) {
                    $weatherArr = json_decode($resp, true);
                    if(isset($weatherArr['main']['temp'])) {
                        $_SESSION['temperature'] = $weatherArr['main']['temp'];
                    }
                }
            } else {
                throw new \RuntimeException('Geo coordinates not found');
            }
            
        } catch (\Exception $ex) {
            $resp['error'] = $ex->getMessage();
            $resp = json_encode($resp);
        }
        
        return $resp;
    }
    
}
