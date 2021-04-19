<?php
namespace helper;

/**
 * Class to get sms template
 */
class SmsTemplate {
    
    /** @var sms template for less then 20 c temperature */
    public $templateLessThen20;
    
    /** @var sms template for greater then 20 c temperature */
    public $templateGreaterThen20;
    
    /** @var current temperature */
    public $temperature;
            
    function __construct($temperature) {
        
        /** Initialize sms templates */
        if(!empty($temperature)) {
            $this->templateGreaterThen20 = "Sandeepa Rajoriya and Temperature more than 20C. $temperature C";
            $this->templateLessThen20 = "Sandeepa Rajoriya and Temperature less than 20C. $temperature C";
            $this->temperature = $temperature;
        }
    }

    /**
     * get sms template by condition
     * @return string
     */
    function getSmsTemplate () {
        if(!empty($this->temperature)) {
            if($this->temperature > 20) {
                return $this->templateGreaterThen20;
            } else {
                return $this->templateLessThen20;
            }
        }
    }
}

