<?php

require_once ROOT_PATH.'/private/PayPalAdaptive/vendor/autoload.php';

//use PayPal\Auth\OAuthTokenCredential;


define('PAYPAL_REDIRECT_URL', 'https://www.sandbox.paypal.com/webscr&cmd=');
define('DEVELOPER_PORTAL', 'https://developer.paypal.com');

class Application_Plugin_PayPalAdaptive{	

	
	// For a full list of configuration parameters refer in wiki page (https://github.com/paypal/sdk-core-php/wiki/Configuring-the-SDK)
	private function getConfig()
	{
		$config = array(
				// values: 'sandbox' for testing
				//		   'live' for production
				"mode" => "sandbox"

				// These values are defaulted in SDK. If you want to override default values, uncomment it and add your value.
				// "http.ConnectionTimeOut" => "5000",
				// "http.Retry" => "2",
			);
		return $config;
	}

	// Creates a configuration array containing credentials and other required configuration parameters.
	private function getAcctAndConfig()
	{
		$config = array(
				// Signature Credential
				"acct1.UserName" => "billing-facilitator_api1.footagezoo.com",
				"acct1.Password" => "RCPLBNYGD6YJRGB6",
				"acct1.Signature" => "AFcWxV21C7fd0v3bYYYRCpSSRl31ArjPB.tSDaavPgmo7YQmNGXPiwrN",
				"acct1.AppId" => "APP-80W284485P519543T"//Live:APP-9VF54141DK250580M

				// Sample Certificate Credential
				// "acct1.UserName" => "certuser_biz_api1.paypal.com",
				// "acct1.Password" => "D6JNKKULHN3G5B8A",
				// Certificate path relative to config folder or absolute path in file system
				// "acct1.CertPath" => "cert_key.pem",
				// "acct1.AppId" => "APP-80W284485P519543T"
				);

		return array_merge($config, self::getConfig());;
	}

	
	
	public function parallelPayment($paymentData = array()){
				
		/*
		 * Use the Pay API operation to transfer funds from a sender’s PayPal account to one or more receivers’ PayPal accounts. You can use the Pay API operation to make simple payments, chained payments, or parallel payments; these payments can be explicitly approved, preapproved, or implicitly approved.
		
		 Use the Pay API operation to transfer funds from a sender's PayPal account to one or more receivers' PayPal accounts. You can use the Pay API operation to make simple payments, chained payments, or parallel payments; these payments can be explicitly approved, preapproved, or implicitly approved. 
		
		 Parallel payments are useful in cases when a buyer intends to make a single payment for items from multiple sellers. Examples include the following scenarios:
		
			a single payment for multiple items from different merchants, such as a combination of items in your inventory and items that partners drop ship for you.
			purchases of items related to an event, such as a trip that requires airfare, car rental, and a hotel booking.
		 
		 * Create your PayRequest message by setting the common fields. If you want more than a simple payment, add fields for the specific kind of request, which include parallel payments, chained payments, implicit payments, and preapproved payments.
		 */
		
		
		define("DEFAULT_SELECT", "- Select -");
		
		
		
		if(isset($paymentData['receiver'])) {

			$receiver = array();
			/*
			 * A receiver's email address 
			 */
			
			$i = 0;
			
			foreach($paymentData['receiver'] as $receivercurr){
				
				$receiver[$i] = new Receiver();
				$receiver[$i]->email = $receivercurr['paypal_email'];
				/*
				 *  	Amount to be credited to the receiver's account 
				 */
				$receiver[$i]->amount = $receivercurr['amount'];
				/*
				 * Set to true to indicate a chained payment; only one receiver can be a primary receiver. Omit this field, or set it to false for simple and parallel payments. 
				 */
				$receiver[$i]->primary = $receivercurr['primary'];
				
				$i++;
			}			 
			
			$receiverList = new ReceiverList($receiver);

		 }
		
		/*
		 * The action for this request. Possible values are:
		
			PAY – Use this option if you are not using the Pay request in combination with ExecutePayment.
			CREATE – Use this option to set up the payment instructions with SetPaymentOptions and then execute the payment at a later time with the ExecutePayment.
			PAY_PRIMARY – For chained payments only, specify this value to delay payments to the secondary receivers; only the payment to the primary receiver is processed.
		
		 */
		/*
		 * The code for the currency in which the payment is made; you can specify only one currency, regardless of the number of receivers 
		 */
		/*
		 * URL to redirect the sender's browser to after canceling the approval for a payment; it is always required but only used for payments that require approval (explicit payments) 
		 */
		/*
		 * URL to redirect the sender's browser to after the sender has logged into PayPal and approved a payment; it is always required but only used if a payment requires explicit approval 
		 */
		 
		
		$payRequest = new PayRequest(new RequestEnvelope("en_US"), $paymentData['actionType'], $paymentData['cancelUrl'], $paymentData['currencyCode'], $receiverList, $paymentData['returnUrl']);
		// Add optional params
		
		
		
		if(isset($paymentData['memo']) && !empty($paymentData['memo'])){
			$payRequest->memo = $paymentData['memo'];
		}
		
		if(isset($paymentData['sender']) && !empty($paymentData['sender'])){
			$payRequest->sender = $paymentData['sender'];
		}
		
		if(isset($paymentData['senderEmail']) && !empty($paymentData['senderEmail'])){
			$payRequest->senderEmail = $paymentData['senderEmail'];
		}
		
		
		 
		/*
		 * 	 ## Creating service wrapper object
		Creating service wrapper object to make API call and loading
		Configuration::getAcctAndConfig() returns array that contains credential and config parameters
		 */
		$service = new AdaptivePaymentsService($this->getAcctAndConfig());
		
		try {
			/* wrap API method calls on the service object with a try catch */
			$response = $service->Pay($payRequest);
			
		} catch (PayPal\Exception\PPConnectionException $ex) {
			
									
		} catch (Exception $ex) {
						
		}
		
		/* Make the call to PayPal to get the Pay token
		 If the API call succeded, then redirect the buyer to PayPal
		to begin to authorize payment.  If an error occured, show the
		resulting errors */

		
		$ack = strtoupper($response->responseEnvelope->ack);
		
		
		
		if($ack != "SUCCESS") {
			
			return (object)array("success"=>false,"message"=>"");
			
		} else {
			
			$payKey = $response->payKey;
			$payPalURL = PAYPAL_REDIRECT_URL . '_ap-payment&paykey=' . $payKey;								
			
			return (object)array("success"=>true,"message"=>"","payurl"=>$payPalURL,"payKey"=>$payKey);
			
		}
		require_once '../Common/Response.php';

		
	}				
	
	
	public function getPaymentDetail($paykey){
		
		/*
		 *  # PaymentDetails API
		 Use the PaymentDetails API operation to obtain information about a payment. You can identify the payment by your tracking ID, the PayPal transaction ID in an IPN message, or the pay key associated with the payment.
		 This sample code uses AdaptivePayments PHP SDK to make API call
		 */
		/*
		 * 
				 PaymentDetailsRequest which takes,
				 `Request Envelope` - Information common to each API operation, such
				 as the language in which an error message is returned.
		 */
		$requestEnvelope = new RequestEnvelope("en_US");
		/*
		 * 		 PaymentDetailsRequest which takes,
				 `Request Envelope` - Information common to each API operation, such
				 as the language in which an error message is returned.
		 */
		$paymentDetailsReq = new PaymentDetailsRequest($requestEnvelope);
		/*
		 * 		 You must specify either,
				
				 * `Pay Key` - The pay key that identifies the payment for which you want to retrieve details. This is the pay key returned in the PayResponse message.
				 * `Transaction ID` - The PayPal transaction ID associated with the payment. The IPN message associated with the payment contains the transaction ID.
				 `paymentDetailsRequest.setTransactionId(transactionId)`
				 * `Tracking ID` - The tracking ID that was specified for this payment in the PayRequest message.
				 `paymentDetailsRequest.setTrackingId(trackingId)`
		 */
		if($paykey != "") {
			$paymentDetailsReq->payKey = $paykey;
		}
		/*if($_POST['transactionId'] != "") {
			$paymentDetailsReq->transactionId = $_POST['transactionId'];
		}
		if($_POST['trackingId'] != "") {
			$paymentDetailsReq->trackingId = $_POST['trackingId'];
		}*/
		
		/*
		 * 	 ## Creating service wrapper object
		Creating service wrapper object to make API call and loading
		Configuration::getAcctAndConfig() returns array that contains credential and config parameters
		 */
		$service = new AdaptivePaymentsService($this->getAcctAndConfig());
		try {
			/* wrap API method calls on the service object with a try catch */
			$response = $service->PaymentDetails($paymentDetailsReq);
			
		} catch(Exception $ex) {
			return (object)array("success"=>false);
			exit;	
		}
		
		$ack = strtoupper($response->responseEnvelope->ack);
		if($ack != "SUCCESS"){
			
			return (object)array("success"=>false);
			
		}else{
			
			return (object)array("success"=>true,"detail"=>$response);
			
		}
		
		
	}
	
	public function executePayment($param){

		/*
		 * The ExecutePayment API operation lets you execute a payment set up with the Pay API operation with the actionType CREATE. To pay receivers identified in the Pay call, set the pay key from the PayResponse message in the ExecutePaymentRequest message.
		
		The ExecutePayment API operation lets you execute a payment set up with the Pay API operation with the actionType CREATE. To pay receivers identified in the Pay call, set the pay key from the PayResponse message in the ExecutePaymentRequest message. 
		 */
		
		/*
		 * (Optional) The pay key that identifies the payment to be executed. This is the pay key returned in the PayResponse message. 
		 */
		$executePaymentRequest = new ExecutePaymentRequest(new RequestEnvelope("en_US"),$param['payKey']);
		$executePaymentRequest->actionType = $param["actionType"];
		/*
		 * The ID of the funding plan from which to make this payment.
		 */
		if(isset($param["fundingPlanID"]) && $param["fundingPlanID"] != "") {
			$executePaymentRequest->fundingPlanId = $param["fundingPlanID"];
		}
		/*
		 * 	 ## Creating service wrapper object
		Creating service wrapper object to make API call and loading
		Configuration::getAcctAndConfig() returns array that contains credential and config parameters
		 */
		$service = new AdaptivePaymentsService($this->getAcctAndConfig());

		try {
			/* wrap API method calls on the service object with a try catch */
			
			$response = $service->ExecutePayment($executePaymentRequest);						
			
			
		} catch (PayPal\Exception\PPConnectionException $ex) {
			
			
									
		} catch (Exception $ex) {
			 
		}
		
		
		$ack = strtoupper($response->responseEnvelope->ack);
		 
		if($ack != "SUCCESS") {
			
			return (object)array("success"=>false,"message"=>"");
			
		} else {						
			
			return (object)array("success"=>true,"message"=>"","data"=>$response);
			
		}
		

	}
		
	public function prepareDirectPayment($paymentData){
							
		if(isset($paymentData['receiver'])) {
			
			$receiver = array();
				$i = 0;
				$receiver[$i] = new Receiver();
				$receiver[$i]->email = $paymentData['receiver']['paypal_email'];
				/*
				 *  	Amount to be credited to the receiver's account 
				 */
				$receiver[$i]->amount = $paymentData['receiver']['amount'];
				/*
				 * Set to true to indicate a chained payment; only one receiver can be a primary receiver. Omit this field, or set it to false for simple and parallel payments. 
				 */
				$receiver[$i]->primary = $paymentData['receiver']['primary'];
		
				/*
				 * (Optional) The invoice number for the payment. This data in this field shows on the Transaction Details report. Maximum length: 127 characters 
				 */
				if(isset($paymentData['invoiceId']) && !empty($paymentData['invoiceId'])){
					
					$receiver[$i]->invoiceId = $paymentData['invoiceId'];
					
				}
				/*
				 * (Optional) The transaction type for the payment. Allowable values are:
		
					GOODS – This is a payment for non-digital goods
					SERVICE – This is a payment for services (default)
					PERSONAL – This is a person-to-person payment
					CASHADVANCE – This is a person-to-person payment for a cash advance
					DIGITALGOODS – This is a payment for digital goods
					BANK_MANAGED_WITHDRAWAL – This is a person-to-person payment for bank withdrawals, available only with special permission.
				
				Note: Person-to-person payments are valid only for parallel payments that have the feesPayer field set to EACHRECEIVER or SENDER.
				 */
				if(isset($paymentData['paymentType']) && !empty($paymentData['paymentType'])) {
					$receiver[$i]->paymentType = $paymentData['paymentType'];
				}
			
				$receiverList = new ReceiverList($receiver);
		 }
		 
		 

		/*
		 * The action for this request. Possible values are:
		
			PAY – Use this option if you are not using the Pay request in combination with ExecutePayment.
			CREATE – Use this option to set up the payment instructions with SetPaymentOptions and then execute the payment at a later time with the ExecutePayment.
			PAY_PRIMARY – For chained payments only, specify this value to delay payments to the secondary receivers; only the payment to the primary receiver is processed.
		
		 */
		/*
		 * The code for the currency in which the payment is made; you can specify only one currency, regardless of the number of receivers 
		 */
		/*
		 * URL to redirect the sender's browser to after canceling the approval for a payment; it is always required but only used for payments that require approval (explicit payments) 
		 */
		/*
		 * URL to redirect the sender's browser to after the sender has logged into PayPal and approved a payment; it is always required but only used if a payment requires explicit approval 
		 */
		$payRequest = new PayRequest(new RequestEnvelope("en_US"), $paymentData['actionType'], $paymentData['cancelUrl'], $paymentData['currencyCode'], $receiverList, $paymentData['returnUrl']);
		// Add optional params
		/*
		 *  (Optional) The payer of PayPal fees. Allowable values are:
		
			SENDER – Sender pays all fees (for personal, implicit simple/parallel payments; do not use for chained or unilateral payments)
			PRIMARYRECEIVER – Primary receiver pays all fees (chained payments only)
			EACHRECEIVER – Each receiver pays their own fee (default, personal and unilateral payments)
			SECONDARYONLY – Secondary receivers pay all fees (use only for chained payments with one secondary receiver)
		
		 */
		if(isset($paymentData['feesPayer']) && !empty($paymentData['feesPayer'])) {
			$payRequest->feesPayer = $paymentData['feesPayer'];
		}
		/*
		 *  (Optional) The key associated with a preapproval for this payment. The preapproval key is required if this is a preapproved payment.
		Note: The Preapproval API is unavailable to API callers with Standard permission levels.
		 */
		if(isset($paymentData['preapprovalKey']) && !empty($paymentData['preapprovalKey'])) {
			$payRequest->preapprovalKey  = $paymentData['preapprovalKey'];
		}
		/*
		 * (Optional) The URL to which you want all IPN messages for this payment to be sent. Maximum length: 1024 characters 
		 */
		if(isset($paymentData['ipnNotificationUrl']) && !empty($paymentData['ipnNotificationUrl'])) {
			$payRequest->ipnNotificationUrl = $_POST['ipnNotificationUrl'];
		}
		
		if(isset($paymentData['memo']) && !empty($paymentData['memo'])) {
			$payRequest->memo = $paymentData["memo"];
		}
		/*
		 *(Optional) A unique ID that you specify to track the payment.
		Note: You are responsible for ensuring that the ID is unique. 
		 */
		if(isset($paymentData['trackingId']) && !empty($paymentData['trackingId'])) {
			$payRequest->trackingId  = $_POST["trackingId"];
		}
		
		$service = new AdaptivePaymentsService($this->getAcctAndConfig());
		
		
		try {
			/* wrap API method calls on the service object with a try catch */
			$response = $service->Pay($payRequest);
			
		} catch (PayPal\Exception\PPConnectionException $ex) {
			
									
		} catch (Exception $ex) {
						
		}
		
		/* Make the call to PayPal to get the Pay token
		 If the API call succeded, then redirect the buyer to PayPal
		to begin to authorize payment.  If an error occured, show the
		resulting errors */
		 
		
		$ack = strtoupper($response->responseEnvelope->ack);
		
		
		
		if($ack != "SUCCESS") {
			
			return (object)array("success"=>false,"message"=>"");
			
		} else {
			
			$payKey = $response->payKey;
			$payPalURL = PAYPAL_REDIRECT_URL . '_ap-payment&paykey=' . $payKey;								
			
			return (object)array("success"=>true,"message"=>"","payurl"=>$payPalURL,"payKey"=>$payKey);
			
		}

		
	}
	

}


