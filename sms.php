<?php
require 'vendor/autoload.php';

include_once dirname(__FILE__) . '/util.php';
// include_once dirname(__FILE__) . '/api/AfricasTalking.php';


use AfricasTalking\SDK\AfricasTalking;

class Sms {

    protected $AT;

    function __construct()
    {
        $this->AT = new AfricasTalking(Util::$API_USERNAME, Util::$API_KEY);
    }

    public function sendSms($message, $recipients){
        //get the sms service
        $sms = $this->AT->sms();
        
        //use the SMS service to send SMS
        $result = $sms->send([
            'to'      => $recipients,
            'message' => $message,
            'from'    => Util::$COMPANY_NAME
        ]);
        return $result;
    }
}

