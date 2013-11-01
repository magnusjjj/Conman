<?php
/**
 * \file PaysonProvider.php
 * This file contains a generic API implementation for Payson.
 * The Payson API works through sending HTTPS-requests to a couple of different addresses, with a couple of extra
 * headers for authentication.
 * Documentation for the Payson API is located at http://api.payson.se/
 * There is currently no real documentation for the generic API this file uses, other than that this file is pretty
 * well commented. If you create a documentation for this, or have tips on how to do it, please send an email to
 * magnusjjj@gmail.com
 * The implementation in this file needs the CURL extension.
 */
class PaysonProvider {
		var $AgentId = ''; ///< One of the two main Payson credentials, the Agent Id identifies what api user you are.
		var $AgentMD5 = ''; ///< The second Payson credential, kind of your password.
        var $url_payforward = ''; ///< The url to send the user to when redirecting to the payment portal.
        var $url_payendpoint = ''; ///< The url to send requests to, to get a token and information about a new payment.
        var $url_paymentdetails = ''; ///< The url to send requests to, to get information about a payment.
        var $url_validate = ''; ///< The url to send requests to validate a IPN request.

        /**
         * Constructor.
         * @param $options      Array with options.
         * @param bool $test    Run in test mode?
         */
        function __construct($options, $test = false){
			if($test)
			{
				$this->AgentId = 1;
				$this->AgentMD5 = 'fddb19ac-7470-42b6-a91d-072cb1495f0a';
				$this->url_payforward = 'https://test-www.payson.se/paysecure/?token={token}';
				$this->url_payendpoint = 'https://test-api.payson.se/1.0/Pay/';
				$this->url_paymentdetails = 'https://test-api.payson.se/1.0/PaymentDetails/';
				$this->url_paymentupdate = 'https://test-api.payson.se/1.0/PaymentUpdate/';
				$this->url_validate = 'https://test-api.payson.se/1.0/Validate/';
			} else {
				$this->AgentId = $options['AccountData']['_AgentId'];
				$this->AgentMD5 = $options['AccountData']['_AgentMD5'];
				$this->url_payforward = 'https://www.payson.se/paySecure/?token={token}';
				$this->url_payendpoint = 'https://api.payson.se/1.0/Pay/';
				$this->url_paymentdetails = 'https://api.payson.se/1.0/PaymentDetails/';
				$this->url_paymentupdate = 'https://api.payson.se/1.0/PaymentUpdate/';
				$this->url_validate = 'https://api.payson.se/1.0/Validate/';
			}
		}


        /**
         * Internal function used to make a payson http request, with the proper credentials added.
         * @param $url              The url to make a request to.
         * @param $content          The content to include in the requests post body.
         * @param bool $unfiltered  Whether or not to parse the content as an array, with parse_str
         * @return array|mixed      Returns the response body.
         */
        function _request($url, $content, $unfiltered = false)
		{
			$result = array();
			$this->ch = curl_init();
			curl_setopt($this->ch, CURLOPT_URL, $url);
			curl_setopt($this->ch, CURLOPT_HEADER, 0);
			curl_setopt($this->ch, CURLOPT_POST, 1);
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $content);
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('PAYSON-SECURITY-USERID: ' . $this->AgentId, 'PAYSON-SECURITY-PASSWORD: ' . $this->AgentMD5, 'Content-Type: '.'application/x-www-form-urlencoded')); 
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
			$r = curl_exec($this->ch);
			if(!$unfiltered)
				parse_str($r, $result);
			else
				$result = $r;
			return $result;
		}

        /**
         * @param array $options    An array of payment options, such as products, names, etc.
         * @return array            Returns an array of response parameters.
         */
        function InitializePayment($options = array()){
            // TODO: Validate the input. Its no big deal right now, but payson will throw a 'Nice' error,
            // TODO: We might want to catch this before even sending the request.

            /* The payment API uses a different, more generic naming in its API than Payson uses,
            but currently uses exactly the same parameter value format.
            The following array describes how to translate the names from what the Payment API expects,
            to what payson expects.
            The first array level is for 'simple' translations of names, a -> b.
            Sub-arrays describe how to deal with translating lists of things like Recievers, and order items.*/
			$options_translate = array(
                'URLReturn' => 'returnUrl',
                'URLCancel' => 'cancelUrl',
                'Description' => 'memo',
                'URLIPN' => 'ipnNotificationUrl',
                'Language' => 'localeCode',
                'Currency' => 'currencyCode',
                'FeePayer' => 'feesPayer',
                'FeeInvoice' => 'invoiceFee',
                'Custom' => 'custom',
                'LocalTrackingID' => 'trackingId',
                'Guarantee' => 'guaranteeOffered',
                'SenderEmail' => 'senderEmail',
                'SenderFirstName' => 'senderFirstName',
                'SenderLastName' => 'senderLastName',
                'Recievers' => array( 'receiverList.receiver(%d)',
                                    array('Email' => 'email',
                                        'FirstName' => 'firstName',
                                        'LastName' => 'lastName',
                                        'Amount' =>  'amount',
                                        'Primary' => 'primary')
                            ),
                'Items' => array('orderItemList.orderItem(%d)',
                                array('Description' => 'description',
                                'SKU' => 'sku',
                                'Quantity' => 'quantity',
                                'Price' => 'unitPrice',
                                'Tax' => 'taxPercentage')
                            )
            );

            // This is the array that will hold the translated names and values:
			$paysonclean = array();

            // Loop through the options, and translate:
			foreach($options as $OptionKey => $OptionValue)
			{
				if(substr($OptionKey, 0, 9) == '__payson_'){ // If its a payson-specific option, we strip it from its header and return it unaltererd.
					$paysonclean[substr($OptionKey, 9)] = $OptionValue;
				} else {
					if(!is_array($OptionValue)) // If its not an array, we can translate it directly.
					{
						$paysonclean[$options_translate[$OptionKey]] = $OptionValue;
					} else {
						// If its an array, we need to loop through the children, and their children in turn
						// The name for the part goes in the format TranslatedName(%i).InnerTranslatedName
						foreach($OptionValue as $key => $InnerItem)
						{
							foreach($InnerItem as $InnerItemParameterName => $InnerItemParameter)
							{
								$paysonclean[sprintf($options_translate[$OptionKey][0], $key) . '.' . $options_translate[$OptionKey][1][$InnerItemParameterName]] = $InnerItemParameter;
							}
						}
					}
				}
			}

            // Now make the request:
			$OriginalResponse =  $this->_request($this->url_payendpoint, http_build_query($paysonclean));

            // Figure out what to return based on the status reported:
			if($OriginalResponse['responseEnvelope_ack'] == 'SUCCESS')
			{
                // Hooray! The request was successfull. We need to return an array with the following:
                // - The status
                // - Where to redirect the user.
                // - What to save in the database (Save, ExternalId, ExternalReference).
                // We save the Payson token in ExternalReference.
				$Returner = array('Status' => 'SUCCESS',
                                  'Redirect' => str_replace('{token}', $OriginalResponse['TOKEN'], $this->url_payforward),
                                  'Save' => $OriginalResponse,
                                  'ExternalId' => @$OriginalResponse['responseEnvelope_correlationId'],
                                  'ExternalReference' => $OriginalResponse['TOKEN']);
			} else {
                // Failure. Save the error, and return.
				$Returner = array('Status' => 'FAILURE', 'Redirect' => NULL, 'Save' => $OriginalResponse);
			}
			return $Returner;
		}

        /**
         * Internal function. Takes the current request, and validates that it came from the Payson server.
         * @return bool       Is it a valid request?
         */
        function _IPNValidate()
		{
			$postData = file_get_contents("php://input");
			$response = $this->_request($this->url_validate, $postData, true);
			if($response == 'VERIFIED')
				return true;
			else
				return false;
		}

        /**
         * Internal function. Used to get information about a Payson payment.
         * Takes a Payson token as a parameter, and then returnes the raw PaymentDetails request.
         * @param $token        The token for the payment to fetch information about.
         * @return array|mixed  The raw PaymentDetails request
         */
        function _PaymentDetails($token)
		{
			$paysonclean = array('token' => $token);
			return $this->_request($this->url_paymentdetails, http_build_query($paysonclean));
		}

        /**
         * Public function to handle an IPN request from Payson. This happens when a payment is updated in any way.
         * @return array    Information about the Payment status, and how to find the order.
         */
        function handleIPN()
        {
            // Check if the payment originated from Payson
            if ($this->_IPNValidate()) {
                $token = $_REQUEST['token']; // The payson token for the order.
                if ($_REQUEST['status'] == 'COMPLETED'){
                    // If the payment is completed, return information about how find the payment.
                    $returner['Status'] = 'COMPLETED';
                    $returner['GetBy'] = 'ExternalReference';
                    $returner['GetValue'] = $token;
                } else {
                    $returner['Status'] = 'FAILURE';
                }
                $returner['OriginalResponse'] = $_REQUEST; // We want to save the whole request, for debugging purposes.
                return $returner;
            }
        }

        /**
         * Public function for handling when the user returns from Payson.
         * @return array    Information about the Payment status, and how to find the order.
         */

        function handleReturn()
		{
			$token = $_REQUEST['TOKEN']; // The payson token for the order.
			$paymentDetailsData = $this->_PaymentDetails($token); // Get payment details about the payment.
			$returner = array('OriginalResponse' => $paymentDetailsData, 'ExternalId' => @$paymentDetailsData['responseEnvelope_correlationId']); // Save the response from Payson for debugging

			if($paymentDetailsData['responseEnvelope_ack'] == 'SUCCESS') // If the request was successfull
			{
				if($paymentDetailsData['status'] == 'COMPLETED'): // Check if the payment was completed
                    // Return information about how to find the payment, and its status.
					$returner['Status'] = 'COMPLETED';
					$returner['GetBy'] = 'ExternalReference';
					$returner['GetValue'] = $token;
				else:
					$returner['Status'] = 'FAILURE';
				endif;
			}

			return $returner;
		}

	}