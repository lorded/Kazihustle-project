<?php
include_once dirname(__FILE__) . '/util.php';
include_once dirname(__FILE__) . '/sms.php';
include_once dirname(__FILE__) . '/mpesa_controller.php';
include_once dirname(__FILE__) . '/bootstrap.php';

include_once dirname(__FILE__) . '/classes/transactions.php';


class Menu{
    protected $text;
    protected $sessionId;

    protected $httpHeaders = array();
    protected $api_url;

    protected $stron_api_url;

    function __construct(){
        $this->httpHeaders = array(
            'Accept: application/json',
            "Content-Type:application/json",
        );

        /* API */
        $this->stron_api_url = 'http://www.server-api.stronpower.com/api/';
    }

    /**
     * Main menu that is displayed once someone initializes the USSD prompt
     */
    public function mainMenu(){
        //shows initial user menu for unregistered users
        $response = "CON Welcome to Greenlife. Reply with\n";
        $response .= "1. Buy Tokens\n";
        echo $response;
    }

    /**
     * Gets the current menu in the USSD prompts
     *
     * @param $textArray
     * @return int
     */
    public function getMenuLevel($textArray)
    {
        return count($textArray);
    }

    /**
     * Handles buying tokens process
     *
     * @param $textArray
     * @param $phoneNumber
     */
    public function buyTokens($textArray, $phoneNumber) {
        $level = $this->getMenuLevel($textArray);
        $selected_option = $textArray[0];
        $meter_id_exists = false;
        $phone_number_exists = false;

        if(1 == $level){
            echo "CON Please enter the meter ID.";
            return;
        }

        $meter_id = $textArray[1];

        /*
         * Check if meter number/id exists
         */
        $meter_id_exists = $this->meterNumberExists($meter_id);

        if(false == $meter_id_exists)
        {
            echo "CON This meter ID does not exist. Reply with 98 to try again";
            return;
        }


        if(2 == $level && $meter_id_exists )
        {
            echo "CON Please enter the token amount.";
            return;
        }

        $token_amount = $textArray[2];

        if(3 == $level && $token_amount && $meter_id_exists)
        {
            /*
             * Trigger STK Push
             */
             $mpesa = new MpesaController();
             $mpesa->generate_token();
             $triggered = $mpesa->trigger_customer_stk_push($phoneNumber, $token_amount, $meter_id);
             
             if(false === $triggered->status)
             {
                 //End USSD Process
                echo 'END Payment initiation process not successful.';
                return;
             }
             
            
            /*
             * STK Process is successful
             *
             * Save requst to db, then run cron job to check transaction status
             */
             $transaction = Transactions::create(array(
                    'checkout_id' => $triggered->request_id,
                    'amount' => $token_amount,
                    'meter' => $meter_id,
                    'phone' => $phoneNumber,
                    'status' => 0
                ));
                
             //End USSD Process
            echo 'END Payment initiation complete. Thanks.';
             

        }
    }

    
    /**
     * Register Menu
     **/ 
    public function registerMenu($textArray, $phoneNumber){
        //building menu for user registration
        $level = count($textArray);
        $option = $textArray[0];


        if($level == 1 && $option == 1){ // Meter Details
            echo "CON Please enter the meter ID";
        } else if($level == 2 && $option == 1){
            $id = $textArray[1];
            if($id < 100)
            {
                $id = sprintf("%03d", $id);
            }
            
            $post_data = array(
                'CompanyName' => 'Greenlife01',
                //'UserName' => 'Quid vending',
                'UserName' => 'Admin019',
                'PassWord' => '123456',
                'MeterId' => $id
            );
            
            $response = $this->request('http://www.server-api.stronpower.com/api/QueryMeterInfo', json_encode($post_data));
            

            echo "END Customer name: " .$response->Customer_name. "\n Customer Id: " .$response->Customer_id. "\n, Meter Type: " .$response->Meter_type. "\n, Price per unit: " .$response->Price_unit. " " .$response->Price. "\n";

        } elseif($level == 1 && $option == 2){ // Customer Details
            echo "CON Please enter the customer ID";
        } else if($level == 2 && $option == 2){
            $id = $textArray[1];
            if($id < 100)
            {
                $id = sprintf("%03d", $id);
            }

            //Contact server and get customer details
            $post_data = array(
                'CompanyName' => 'Greenlife01',
                //'UserName' => 'Quid vending',
                'UserName' => 'Admin019',
                'PassWord' => '123456',
                'CustomerId' => $id
            );
            $response = $this->request('http://www.server-api.stronpower.com/api/QueryCustomerInfo', json_encode($post_data));

            echo 'END Customer name: ' .$response->Customer_name. "\n Meter Type: " .$response->Meter_type. "\n Meter ID: " .$response->Meter_id. "\n Price per unit: " .$response->Price_unit. " " .$response->Price. "\n";

        } else {
            echo "END Invalid entry";
        }
    }

    public function addCountryCodeToPhoneNumber($phone){
        return Util::$COUNTRY_CODE . substr($phone, 1);
    }

    public function middleware($text){
        //remove entries for going back and going to the main menu
        return $this->goBack($this->goToMainMenu($text));
    }

    public function goBack($text){
        //1*4*5*1*98*2*1234
        $explodedText = explode("*",$text);
        while(array_search(Util::$GO_BACK, $explodedText) != false){
            $firstIndex = array_search(Util::$GO_BACK, $explodedText);
            array_splice($explodedText, $firstIndex-1, 2);
        }
        return join("*", $explodedText);
    }

    public function goToMainMenu($text){
        //1*4*5*1*99*2*1234*99
        $explodedText = explode("*",$text);
        while(array_search(Util::$GO_TO_MAIN_MENU, $explodedText) != false){
            $firstIndex = array_search(Util::$GO_TO_MAIN_MENU, $explodedText);
            $explodedText = array_slice($explodedText, $firstIndex + 1);
        }
        return join("*",$explodedText);
    }

    /**
     * Check if meter ID exists from Stron API
     *
     * @param $meter_id
     * @return bool
     */
    public function meterNumberExists($meter_id)
    {
        $meter_exists = false;

        $query_meter_url = $this->stron_api_url . 'QueryMeterInfo';
        $query_meter_data = array(
            'MeterId' => $meter_id,
        );

        $meter_details = $this->request($query_meter_url, 'POST', $query_meter_data, true);

        if($meter_id == $meter_details[0]->Meter_id)
        {
            $meter_exists = true;
        }

        return $meter_exists;
    }


    /**
     * Makes API Request
     *
     * @param $url
     * @param $method
     * @param array $post_data
     * @param false $stron_api_call
     * @return mixed
     */
    public function request($url, $method, array $post_data = array(), bool $stron_api_call = false)
    {
        $post_methods = array('POST', 'PATCH', 'PUT');

        if(true == $stron_api_call)
        {
            $post_data['CompanyName'] = 'Greenlife01';
            $post_data['UserName'] = 'Admin019';
            $post_data['PassWord'] = '123456';
        }

        $ch = curl_init();

        if(in_array(strtoupper($method), $post_methods))
        {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->httpHeaders);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($ch);

        $eno = curl_errno($ch);
        $emes = curl_error($ch);
        curl_close($ch);

        return json_decode($content);
    }
}
