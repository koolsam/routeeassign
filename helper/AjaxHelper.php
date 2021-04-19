<?php
namespace helper;
require_once ("../helper/Weather.php");
require_once ("../helper/SmsHelper.php");
use helper\Weather;
use helper\SmsHelper;

/**
 * Helper to handle all ajax calls for application
 */
class AjaxHelper {
    
    /** 
     * function to create instance of given class
     * and call method of this class send in post data
     * @throws \RuntimeException 
     */
    
    function callClassMethod () {
        $resp = [];
        try {
            if(!empty($_POST['class']) && !empty($_POST['method'])) {
                $className = $_POST['class'];
                $methodName = $_POST['method'];
                
                /** check if class with $className exits 
                 * if yes then create instance and check 
                 * if method with $methodName exists in the class instance
                 * if not then @throws \RuntimeException
                 */
                if(class_exists($className)) {
                    $obj = new $className();
                    if(method_exists($obj, $methodName)) {
                        $resp = $obj->{$methodName}();
                    } else {
                        throw new \RuntimeException('there is some issue please try after some time');
                    }
                } else {
                    throw new \RuntimeException('there is some issue please try after some time');
                }
            } else {
                throw new \RuntimeException('wrong data in request');
            }
        } catch (\Exception $ex) {
            $resp['error'] = $ex->getMessage();
            $resp = json_encode($resp);
        }
        print_r($resp);
    }
}

/** call for AjaxHelper */
$ajaxObj = new AjaxHelper();
$ajaxObj->callClassMethod();