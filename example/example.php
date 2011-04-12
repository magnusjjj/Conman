<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
 * Payson API Integration example for PHP
 *
 * More information can be found att https://api.payson.se
 *
 */

/*
 * On every page you need to use the API you
 * need to include the file lib/paysonapi.php
 * from where you installed it.
 */

require_once '../lib/paysonapi.php';

/* Every interaction with Payson goes through the PaysonApi object which you set up as follows */
$credentials = new PaysonCredentials("<your api userid>", "<your api password>");
$api = new PaysonApi($credentials);

/*
 * To initiate a direct payment the steps are as follows
 *  1. Set up the details for the payment
 *  2. Initiate payment with Payson
 *  3. Verify that it suceeded
 *  4. Forward the user to Payson to complete the payment
 */

/*
 * Step 1: Set up details
 */

// URLs to which Payson sends the user depending on the success of the payment
$returnUrl = "http://localhost/return.php";
$cancelUrl = "http://localhost/cancel.php";
// URL to which Payson issues the IPN
$ipnUrl = "http://localhost/ipn.php";

// Details about the receiver
$receiver = new Receiver(
    "<your payson account>", // The email of the account to receive the money
    100); // The amount you want to charge the user, here in SEK (the default currency)
$receivers = array();

// Details about the user that is the sender of the money
$sender = new Sender("<sender email", "<sender firstname", "<sender lastname>");

print("\nPay:\n");

$payData = new PayData($returnUrl, $cancelUrl, $ipnUrl, "description", $sender, $receivers);

/*
 * Step 2 initiate payment
 */
$payResponse = $api->pay($payData);


/*
 * Step 3: verify that it suceeded
 */
if ($payResponse->getResponseEnvelope()->wasSuccessful())
{
    /*
     * Step 4: forward user
     */
    header("Location: " . $api->getForwardPayUrl($payResponse));
}

?>