<?php
	class Settings
	{
		static $PayAPI = array('Name' => 'Payson',
			'Test' => true,
			'_application_email' => 'yourpaysonemail@mail.com');

		static $MembershipCost = 50; // The membership cost

		static $ConEnds = "2012-01-08"; // The date the event ends. Used to calculate when a member needs to renew their membership.

		static $ErrorReporting = E_ALL; // Debugging, error_reporting

		static $EventName = "Tuxtest"; // Name of the event
		static $Society = "Tuxie"; // Name of those that hold the event
		static $Template = "newer"; // Template used :)
		static $AllowPayson = true; // Is payson used for the final payment?
		static $RequireEmail = true; // Do you require email activation?
				
		static $CustomerserviceUrl = "http://www.test.com"; // URL to external customer service site
		static $CustomerserviceEmail = "magnusjjj@jgl.se"; // Email to customer service
		static $TechEmail = "test@test.nu"; // Email to technical support
		static $StatutesUrl = "http://www.test2.com"; // URL to statutes document
		static $TermsUrl = "http://www.test3.com"; // URL to terms of purchase
		
		static $BarKey = ''; // Key for encoding the barcode.	}
	}
?>
