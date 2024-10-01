<?php
include_once dirname(__FILE__) . '/util.php';
include_once dirname(__FILE__) . '/menu.php';
include_once dirname(__FILE__) . '/sms.php';
include_once dirname(__FILE__) . '/mpesa_controller.php';
include_once dirname(__FILE__) . '/bootstrap.php';

include_once dirname(__FILE__) . '/classes/transactions.php';

$menu = new Menu();
$mpesa = new MpesaController();
$mpesa->generate_token();

/**
* Get all transactions whose status is 0 - i.e. not confirmed
*/ 
$date = new \DateTime();
$date->modify('-2 hours');
$formatted_date = $date->format('Y-m-d H:i:s');
$transactions = Transactions::where('status', 0)->where('created_at', '>', $formatted_date )->get();

error_log('Cron is running');

foreach($transactions as $transaction)
{
    
    if($transaction->status == 0)
    {
        // Check status
        $payment_status = $mpesa->confirm_payment($transaction->checkout_id);
        
        if(false == $payment_status)
        {
            continue;
        }
        
        
        if( $payment_status == true)
        {
            
            $token_post_data = array(
                'CompanyName' => 'Greenlife01',
                'UserName' => 'Admin019',
                'PassWord' => '123456',
                'MeterId' => $transaction->meter,
                'is_vend_by_unit' => 'kWh',
                'amount' => $transaction->amount,
                'created_at' => date('m/d/Y h:i:s a', time()),
                'updated_at' => date('m/d/Y h:i:s a', time())
            );
            
            $token_details_url = 'http://www.server-api.stronpower.com/api/' . 'VendingMeter';
            $token_details = $menu->request($token_details_url, 'POST', $token_post_data)[0];
            
            error_log(print_r($token_details, true));
        
            if(null == $token_details)
            {
                continue;
            }
            
            //Send token info via SMS
            $message = "Eco Solutions Token\n";
            $message .= "Mtr No: " .$transaction->meter . "\n";
            $message .= "Token Amount KShs: " . $transaction->amount . "\n";
            $message .= "Token: " . $token_details->Token . "\n";
            $message .= "Date: " . date('m/d/Y h:i:s a', strtotime('+3 hours')) . "\n";
            $message .= "Units: " . round($token_details->Total_unit, 6) . "\n";
            $message .= "Thanks.";
            
            $transaction->status = 1;
            $saved = $transaction->save();
            
            if( $saved )
            {
                $sms = new SMS();
                $sms->sendSms($message, $transaction->phone);
            }
    		
            
        }
    }
}
