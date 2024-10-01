<?php

use Carbon\Carbon;

class MpesaController
{
    /**
     * Get M-Pesa Token
     * @return mixed
     */
    public function generate_token()
    {
        $consumer_key="glOkub0auY0ISOCTGbrGGHCT9scCGy1U";
        $consumer_secret="xx1N3WFbJmtlhIM9";
        $credentials = base64_encode($consumer_key.":".$consumer_secret);

        $url = "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic ".$credentials));
        curl_setopt($curl, CURLOPT_HEADER,false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $access_token=json_decode($curl_response);
        return $access_token->access_token;

    }

    /**
     * Lipa na M-PESA password
     *
     */
    public function lipa_na_mpesa_password()
    {
        $lipa_time = Carbon::rawParse('now')->format('YmdHms');
        $passkey = "8fc2b436222174feae5c0dfb06f0847bb9d5d0fff4956760f0389ac843e64cfe";
        $BusinessShortCode = 4087381;
        $timestamp = $lipa_time;
        $lipa_na_mpesa_password = base64_encode($BusinessShortCode.$passkey.$timestamp);
        return $lipa_na_mpesa_password;
    }

    public function trigger_customer_stk_push($phone_number, $amount, $account_number)
    {
        $url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$this->generate_token()));
        $curl_post_data = [
            //Fill in the request parameters with valid values
            'BusinessShortCode' => 4087381,
            'Password' => $this->lipa_na_mpesa_password(),
            'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone_number, // replace this with your phone number
            'PartyB' => 4087381,
            'PhoneNumber' => $phone_number, // replace this with your phone number
            'CallBackURL' => 'https://vendor.hostfiti.com',
            'AccountReference' => $account_number,
            'TransactionDesc' => "Token Payment"
        ];

        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);
        
        curl_close($curl);
        
        $result = json_decode($curl_response, true);
        
        $erros = '';
        
        $stk_triggered = new StdClass();
        $stk_triggered->status = false;
        $stk_triggered->msg = '';
        $stk_triggered->request_id = $result['CheckoutRequestID'];
        
        if (isset($result['ResponseCode']) && $result['ResponseCode'] == 0) {
           $stk_triggered->status = true;
        } elseif ($result['errorCode'] && $result['errorCode'] == '500.001.1001') {
            $errors = "Error! A transaction is already in progress for the current phone number";
            $stk_triggered->msg = $errors;
           
        } elseif ($result['errorCode'] && $result['errorCode'] == '400.002.02') {
            $errors = "Error! Invalid Request";
            
        } else {
            $errors = "Error! Unable to make MPESA STK Push request. If the problem persists, please contact our site administrator!";
            $stk_triggered->msg = $errors;
        }
        
        
        return $stk_triggered;
    }
    
    
    /**
     * Check if an actual payment was made
     * 
     * */
     public function confirm_payment($checkout_request_id)
     {
        $url = 'https://api.safaricom.co.ke/mpesa/stkpushquery/v1/query';
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$this->generate_token()));
        $curl_post_data = [
            //Fill in the request parameters with valid values
            'BusinessShortCode' => 4087381,
            'Password' => $this->lipa_na_mpesa_password(),
            'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            'CheckoutRequestID' => $checkout_request_id,
        ];

        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);
        
        curl_close($curl);
        
        $result = json_decode($curl_response, true);
        error_log('Transaction details of request id: ' . $checkout_request_id);
        error_log(print_r($result, true));
        
        $payment_made = false;
        
        if(isset($result['ResultCode']) && $result['ResultCode'] == 0)
        {
            $payment_made = true;
        }
        
        return $payment_made;
        
     }
     
     
    /**
     * J-son Response to M-pesa API feedback - Success or Failure
     */
    public function create_validation_response($result_code, $result_description){
        $result = json_encode(["ResultCode"=>$result_code, "ResultDesc"=>$result_description]);
        $response = new Response();
        $response->headers->set("Content-Type","application/json; charset=utf-8");
        $response->setContent($result);
        return $response;
    }
    /**
     *  M-pesa Validation Method
     * 
     *  Safaricom will only call your validation if you have requested by writing an official letter to them
     */
    public function mpesa_validation(Request $request)
    {
        $result_code = "0";
        $result_description = "Accepted validation request.";
        return $this->create_validation_response($result_code, $result_description);
    }
    /**
     * M-pesa Transaction confirmation method, we save the transaction in our databases
     */
    public function mpesa_confirmation(Request $request)
    {
        $content = json_decode($request->getContent());

        $mpesa_transaction = new Mpesa_Transaction();
        $mpesa_transaction->TransactionType = $content->TransactionType;
        $mpesa_transaction->TransID = $content->TransID;
        $mpesa_transaction->TransTime = $content->TransTime;
        $mpesa_transaction->TransAmount = $content->TransAmount;
        $mpesa_transaction->BusinessShortCode = $content->BusinessShortCode;
        $mpesa_transaction->BillRefNumber = $content->BillRefNumber;
        $mpesa_transaction->InvoiceNumber = $content->InvoiceNumber;
        $mpesa_transaction->OrgAccountBalance = $content->OrgAccountBalance;
        $mpesa_transaction->ThirdPartyTransID = $content->ThirdPartyTransID;
        $mpesa_transaction->MSISDN = $content->MSISDN;
        $mpesa_transaction->FirstName = $content->FirstName;
        $mpesa_transaction->MiddleName = $content->MiddleName;
        $mpesa_transaction->LastName = $content->LastName;
        $mpesa_transaction->save();

        // Responding to the confirmation request
        $response = new Response();
        $response->headers->set("Content-Type","text/xml; charset=utf-8");
        $response->setContent(json_encode(["C2BPaymentConfirmationResult"=>"Success"]));
        return $response;
    }

    /**
     * M-pesa Register Validation and Confirmation method
     */
    public function mpesa_register_urls()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization: Bearer '. $this->generate_token()));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            'ShortCode' => "4087381",
            'ResponseType' => 'Completed',
            'ConfirmationURL' => "https://vendor.afoci.ga/public/api/payment/confirmation",
            'ValidationURL' => "https://vendor.afoci.ga/public/api/payment/validation"
        )));
        $curl_response = curl_exec($curl);
        echo $curl_response;
    }
}
