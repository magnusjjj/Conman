<?php
class TicketController extends Controller 
{
	private function _checkLogin() // Function for kicking out the user if its not logged in.
	{
		if (!Auth::user()) {
			$this->_redirect('/index/kicked');
			ErrorHelper::error('Du är utloggad!');
			return false;
		} else {
			return true;
		}
	}
	
	private function _buildAlternativeTree() // Function used for building a tree of different ticket types
	{
		$alternatives = Model::getModel('ordersalternatives');
		$the_alternatives = $alternatives->getAlternativesWithUserCount(Auth::user());
		$tree_parents = array();
		$tree_children = array();
		$tree_simple = array();
		foreach ($the_alternatives as $alternative) {
			if (($alternative['ammount'] != 0 && $alternative['ammount_compare'] >= $alternative['ammount'])
				|| 
				($alternative['max_per_user'] != 0 && $alternative['users_count'] >= $alternative['max_per_user'])
			) {
				if ($alternative['template_override'] == 'ticket') {
					$this->_set('ticket_disabled', true);
				} else {
					
				}
			} else {
				if (!empty($alternative['parent'])) {
					$tree_children[$alternative['parent']][] = $alternative;
				} else {
					$tree_parents[] = $alternative;
				}
				
				$tree_simple[$alternative['id']] = $alternative;
			}
		}
		$this->tree_parents = $tree_parents;
		$this->tree_children = $tree_children;
		$this->tree_simple = $tree_simple;
	}

        private function _buildAlternativeTreeNoFilter() // Function used for building a tree of different ticket types
        {
                $alternatives = Model::getModel('ordersalternatives');
                $the_alternatives = $alternatives->getAlternativesWithUserCount(Auth::user());
                $tree_parents = array();
                $tree_children = array();
                $tree_simple = array();
                foreach ($the_alternatives as $alternative) {
                                if (!empty($alternative['parent'])) {
                                        $tree_children[$alternative['parent']][] = $alternative;
                                } else {
                                        $tree_parents[] = $alternative;
                                }

                                $tree_simple[$alternative['id']] = $alternative;
                }
                $this->tree_parents = $tree_parents;
                $this->tree_children = $tree_children;
                $this->tree_simple = $tree_simple;
        }


	private function _checkMembership($member) // Used for checking if the users membership is outdated.
	{
		$memdate = strtotime($member['membershipEnds']);
		$sysdate = strtotime(Settings::$ConEnds);
		if ($memdate < $sysdate)
			return false;
		else
			return true;
	}
	
	public function index() // The index page. Displayed at the time that the user logged in.
	{
		if (!$this->_checkLogin()) // Kick the user if its not logged in. 
			return;

		$order = Model::getModel('order');
		$myorders = $order->getOrdersByUserId(Auth::user());
		$ordersvalues = Model::getModel('ordersvalues');
		foreach($myorders as $i => $order){
			if($order['status'] != 'COMPLETED')
				unset($myorders[$i]);
		}
		
		if (count($myorders)) {
              		$boughtticket = false;
       		        $mashup = array();
        	        foreach($myorders as $myorder)
	                {
	                        $the_ordersvalues = $ordersvalues->getOrderValuesFromOrder($myorder['id']);
	                        foreach ($the_ordersvalues as $value) {
	                                if($value['id'] == 2 || $value['id'] == 33) {
	                                        $boughtticket = true;
	                                }
	                        }
	                }
			$this->_set('boughtticket', $boughtticket);


			$this->view = 'ticket.hasticket.php';
			$this->_buildAlternativeTree();
			$this->_set('orders', $myorders);
			$ordersvalues = Model::getModel('ordersvalues');
			$ordersvalues_complete = array();
			foreach($myorders as $key => $the_order)
			{
				$ordersvalues_complete[$key] = $ordersvalues->getOrderValuesFromOrder($the_order['id']);
			}
			$this->_set('ordersvalues', $ordersvalues_complete);
			$this->_set('alternatives_simple', $this->tree_simple);
		} else {
			$this->_redirect('buystuff');
		}
	}

	function buystuff()
	{
		if (!$this->_checkLogin()) // Kick the user if its not logged in. 
		    return;
		$this->_buildAlternativeTree();
		$member = Model::getModel('member');
                $themember = $member->getMemberByUserID(Auth::user());

                $this->_set('is_member', $this->_checkMembership($themember));
                $this->_set('alternatives_parents', $this->tree_parents);
                $this->_set('alternatives_children', $this->tree_children);
	}
	
	function buystuff_info()
	{
		if (!$this->_checkLogin()) // Kick the user if its not logged in. 
		    return;
	}
	
	public function gotopay()
	{
		if (!$this->_checkLogin()) 
			return;
		// Import the payson api
		require_once(Settings::getRoot() . '/includes/paysonapi/lib/paysonapi.php');
		// Get the models
		$alternatives = Model::getModel('ordersalternatives');
		$order = Model::getModel('order');
		$member = Model::getModel('member');
		$ordervalues = Model::getModel('ordersvalues');
		$orderscodes= Model::getModel('orderscodes');
		
		$code_id = 0;
		$code_reduction = 0;
		
		// Get the different values, used later
		$themember = $member->getMemberByUserID(Auth::user()); // Get the member from the database corresponding to the user.
		$the_alternatives = $alternatives->getAlternatives();
		
		if (empty($_REQUEST['iaccept'])) { // User must accept the agreement
                        $this->_set('error', 'Du måste acceptera köpvillkoren!');
                        $this->view = 'ticket.buystuff.php';
                        $this->buystuff();
                        return;
                }

		/*if(empty($_REQUEST['code']))
		{
                        $this->_set('error', 'Du måste ha en kod under förköpet!');
                        $this->view = 'ticket.buystuff.php';
                        $this->buystuff();
                        return;
		}*/

		if(!empty($_REQUEST['code']))
		{
		    $the_code = $orderscodes->getCode($_REQUEST['code']);
		    if(empty($the_code)){
			$this->_set('error', 'Den koden finns inte!'); 
			$this->view = 'ticket.buystuff.php';
			$this->buystuff();
			return;
		    } else {
			if($the_code['used_by']){
			    $this->_set('error', 'Den koden är redan använd!'); 
			    $this->view = 'ticket.buystuff.php';
			    $this->buystuff();
			    return;
			}
			$code_id = $the_code['id'];
			$code_reduction = $the_code['reduction'];
		    }
		}
		
		
		
		$thingstobuy = @$_REQUEST['val']; // This will be the array with the things the user has selected to buy
		$numbuy = @$_REQUEST['ammount'];
		
		if (empty($thingstobuy)) { // You will, of course, have to buy something
			$this->_set('error', 'Du har inte markerat något du vill köpa!'); 
			$this->view = 'ticket.buystuff.php';
			$this->buystuff();
			return;
		}
		
		$num_outer = 0;
		foreach($numbuy as $num){
			if(is_numeric($num) && $num > 0)
			{
				$num_outer = $num;
			}
		}
		
		if($num_outer == 0)
		{
			$this->_set('error', 'Du har inte markerat något du vill köpa!'); 
			$this->view = 'ticket.buystuff.php';
			$this->buystuff();
			return;
		}
		

		// Building some parent/children trees for easy keeping
		$tree_all = array(); // Indexed by the alternative id's
		$tree_parents = array(); // All the root parents get stuck into this
		$tree_children = array(); // Indexed by the parent id is the children. Two layer.
		foreach ($the_alternatives as $alternative) {
			$tree_all[$alternative['id']] = $alternative;
			if (!empty($alternative['parent'])) {
				$tree_children[$alternative['parent']][] = $alternative;
			} else {
				$tree_parents[] = $alternative;
			}
		}
		
		$cost = 0; // This will contain the total cost
		$stuff = array(); // This will contain the payson OrderItem's.
		try {
			foreach ($thingstobuy as $key => $thing) {
				if(@$numbuy[$key] > 0)
				{
					if (is_numeric($thing)) { // If its a select-box, we get the price of the option
						$cost += $numbuy[$key] * $tree_all[$thing]['cost']; // Add the cost
						$stuff[] = new OrderItem($tree_all[$thing]['name'], $tree_all[$thing]['cost'], $numbuy[$key], 0, str_pad($key, 6, '0', STR_PAD_LEFT)); // Create the payson order object
					} else if (is_array($thing)) {  // Its not a default action, like a checkbox or a select, but an override
						include_once(dirname(__FILE__) . '/itemtypes/overrides/'.$tree_all[$key]['template_override'].'.php'); // Include the override
						$classname = ucfirst($tree_all[$key]['template_override']) . 'OrderItem'; // Create the override classname
						$itemclass = new $classname($key, $thing, $tree_all); // Init the classname with the id, the object parameters, and the tree
						while (($i = $itemclass->getItem()) !== null) { // Parse the results
							$cost += $i['cost'] * $i['number']; // Add the cost
							$stuff[] = new OrderItem($i['name'], $i['cost'], $i['number'], 0, str_pad($key, 6, '0', STR_PAD_LEFT)); // Create the payson order object
						}
					} else if ($thing != 'NULL') { // Otherwise its a checkbox, get its stuff here
						$cost += $numbuy[$key] * $tree_all[$key]['cost']; // Add the cost
						$stuff[] = new OrderItem($tree_all[$key]['name'], $tree_all[$key]['cost'], $numbuy[$key], 0, str_pad($key, 6, '0', STR_PAD_LEFT)); // Create the payson order object
					} else {
						unset($thingstobuy[$key]);
						unset($numbuy[$key]);
					        if (empty($thingstobuy)) { // You will, of course, have to buy something
                				        $this->_set('error', 'Du måste köpa något! 3');
                        				$this->view = 'ticket.buystuff.php';
                       					$this->buystuff();
                        				return;
                				}
					}
				}
			}
		} catch (Exception $e) {
			$this->_set('error', $e->getMessage()); // Catch errors from the overrides
			$this->view = 'ticket.buystuff.php';
			$this->buystuff();
			return;
		}
		
		
		if (!$this->_checkMembership($themember)) { // Check if the person is a payed-up member or not.
			//$cost += Settings::$MembershipCost; // Else, add the membership cost
			//$stuff[] = new OrderItem('Medlemskap i föreningen', Settings::$MembershipCost, 1, 0, str_pad('80085', 6, '0', STR_PAD_LEFT)); // Create the payson Order-item
		}
		
		if($code_reduction)
		{
		    $cost -= $code_reduction;
		    $stuff[] = new OrderItem('Kodrabatt', 0 - $code_reduction, 1, 0, str_pad('80086', 6, '0', STR_PAD_LEFT));
		}
		
		/* Every interaction with Payson goes through the PaysonApi object which you set up as follows */
		$credentials = new PaysonCredentials(Settings::$PaysonAgentID, Settings::$PaysonMD5);
		$api = new PaysonApi($credentials);

		// URLs to which Payson sends the user depending on the success of the payment
		$returnUrl = Router::url('pay_return', true);
		$cancelUrl = Router::url('pay_cancel',true);
		// URL to which Payson issues the IPN
		$ipnUrl = Router::url('pay_status', true);


		// Details about the receiver
		$receiver = new Receiver(
			Settings::$PaysonMail, // The email of the account to receive the money
			$cost
		); // The amount you want to charge the user, here in SEK (the default currency)
		$receivers = array();
		$receivers[] = $receiver;
		$sender = new Sender($themember['eMail'], $themember['firstName'], $themember['lastName']);
		$payData = new PayData($returnUrl, $cancelUrl, $ipnUrl, "Biljett", $sender, $receivers);
		$payData->setguaranteeOffered("NO");
		$payData->setOrderItems($stuff);
		$payData->setFeesPayer("SENDER");
		$payResponse = $api->pay($payData);
		

		if ($cost <= 0 || (isset(Settings::$AllowPayson) && !Settings::$AllowPayson) 
			|| $payResponse->getResponseEnvelope()->wasSuccessful()
		) { // If payson is dissallowed, or the payson order call was successfull, we create the actuall order in the database
			$order_id = $order->addOrder(array('user_id' => Auth::user(),
							   'payson_token' => (isset(Settings::$AllowPayson) && !Settings::$AllowPayson) ? '' : $payResponse->getToken(),
							   'code_id' => $code_id
				));
			foreach ($thingstobuy as $key => $thing) {
				if(@$numbuy[$key] != 0)
				{
					$id = null;
					$value = null;
				
					if (is_numeric($thing)) { // If its a select-box, we use the value as the id
						$id = $thing;
						$value = '';
					} else if (is_array($thing)) {  // Its not a default action, like a checkbox or a select, but an override
						$id = $key;
						$value = serialize($thing);
					} else { // Otherwise its a checkbox, get its stuff here
						$id = $key;
						$value = '';
					}
					$ordervalues->addOrderValue(array('order_id' => $order_id, 'order_alternative_id' => $id, 'value' => $value, 'ammount' => $numbuy[$key]));
				}
			}
			
			
			if (!$this->_checkMembership($themember)) {
			    //$ordervalues->addOrderValue(array('order_id' => $order_id, 'order_alternative_id' => 0, 'value' => 'MEMBERSHIP'));
			}
			
			if($cost <= 0)
			{
				
				$this->view = 'ticket.index.php';
				$this->_doCompleteOrder($order_id);
				$this->index();
				return;
			}
			
			if (!( isset(Settings::$AllowPayson) && !Settings::$AllowPayson )) { // If we allow payson, redirect the user there
				header("Location: " . $api->getForwardPayUrl($payResponse)); // If we allow payson, redirect the user there
				$this->_set('link',  $api->getForwardPayUrl($payResponse)); // Link for people without auto redirect. Grumble grumble.
			} else {
				$order->setStatusById($order_id, 'MANUALNCOMPLETED');
				$this->_set('order_id', $order_id);
				$this->_set('member', $themember);
			}
		//	var_dump($cost);
		//	var_dump($stuff);
		//	var_dump($numbuy);
		} else {
			ErrorHelper::error("Något gick fel med vår kontakt till Payson");
//			var_dump($payResponse);
		}
	}
	
	private function _doCompleteOrder($orderid)
	{	
		$order = Model::getModel('order');
		$member = Model::getModel('member');
		$ordervalues = Model::getModel('ordersvalues');
		$orderscodes = Model::getModel('orderscodes');
		
		$order->setStatusById($orderid, 'COMPLETED');
		$the_order = $order->getOrderById($orderid);
		$the_ordersvalues = $ordervalues->getOrderValuesFromOrder($orderid);
		
		if($the_order['code_id'])
		{
		    $orderscodes->markCode($the_order['code_id'], Auth::user());
		}
		foreach($the_ordersvalues as $order_value){
		    if($order_value['id'] == 0 && $order_value['value'] == 'MEMBERSHIP'){
			$themember = $member->getMemberByUserID(Auth::user());
			$member->updateMemberShip($themember['PersonID']);;
		    }
		}
	}
	
	public function pay_return($override = false)
	{
		if(!$this->_checkLogin()) 
			return;
		require_once(Settings::getRoot() . '/includes/paysonapi/lib/paysonapi.php');
		$credentials = new PaysonCredentials(Settings::$PaysonAgentID, Settings::$PaysonMD5);
		$api = new PaysonApi($credentials);
		
		$order = Model::getModel('order');
		$member = Model::getModel('member');
		$ordervalues = Model::getModel('ordersvalues');
		$orderscodes = Model::getModel('orderscodes');
		
		$paymentDetailsData = new PaymentDetailsData($_REQUEST['TOKEN']);
		$paymentDetailsResponse = $api->paymentDetails($paymentDetailsData);
		$paydetails = $paymentDetailsResponse->getPaymentDetails();
		if ($paydetails->getStatus() == 'COMPLETED') {
			$the_order = $order->getOrderByToken($paydetails->getToken());
			$this->_doCompleteOrder($the_order['id']);
		}
		$this->_set('status', $paydetails->getStatus());
		$this->view = 'ticket.index.php';
		$this->index();
	}
	
	public function pay_cancel()
	{
                $this->_set('error', 'Ditt köp blev avbrutet. Försök igen.');
                $this->view = 'ticket.buystuff.php';
                $this->buystuff();
	}
	
	public function pay_status() // This does nothing, because of payson uncertainty
	{
		require_once(Settings::getRoot() . '/includes/paysonapi/lib/paysonapi.php');
		$order = Model::getModel('order');
		$trace = Model::getModel('paysontrace');
		// Get the POST data
		$postData = file_get_contents("php://input");

		// Set up API
		$credentials = new PaysonCredentials(Settings::$PaysonAgentID, Settings::$PaysonMD5);
		$api = new PaysonApi($credentials);

		// Validate the request
		$response =  $api->validate($postData);
		if ($response->isVerified()) {
			error_log("inne i verifieringen");
			// IPN request is verified with Payson
			// Check details to find out what happened with the payment
			$details = $response->getPaymentDetails();
			error_log("detaljer");
			error_log($details->status.$details->token);
			$order->setStatus( $details->getToken(), $details->getStatus()); 
			$trace->log(serialize($details));
			error_log("serilizerat");
		}

	}

	public function move()
	{
	
		if(!$this->_checkLogin()) // Am i logged in?
			return;

		// Import models
		$order = Model::getModel('order');
		$ordersvalues = Model::getModel('ordersvalues');
		$user = Model::getModel('user');
		$logger = Model::getModel('log');

		// Get a list of the orders that are completed.
		$myorders = $order->getOrderFromUserAndStatus(Auth::user(), 'COMPLETED');

		// If we can't find any orders, display an error:
		if(!count($myorders))
		{
			ErrorHelper::error("Du har ingen order, eller är inte inloggad. Vid frågor, kontakta kundtjanst@narcon.se");
			return;
		}
		
		// Get a list of all the different order alternatives
		$this->_buildAlternativeTreeNoFilter();
		
		// We are going to store a list with all the ordersvalues combined in this, with all the values combined.
		$mashup = array();
		
		foreach($myorders as $myorder)
		{
			$the_ordersvalues = $ordersvalues->getOrderValuesFromOrder($myorder['id']);
			foreach ($the_ordersvalues as $value)
			{
				if(empty($mashup[$value['id']])) // Create an ordersvaluesarray with all the values combined.
				{
					$mashup[$value['id']] = $value;
				} else {
					$mashup[$value['id']]['ammount'] += $value['ammount']; 
				}
			}
		}
		

		// Send it to the view
		$this->_set('ordersvalues', $mashup);
		$this->_set('tree_simple', $this->tree_simple);

		// If the user has posted a request for moving of a ticket..
		if(!empty($_REQUEST['ammount']))
		{
			// Check if the user exists, and sanitycheck.
			$moveto = $user->getByUsernameOrEmail(@$_REQUEST['usertomoveto']);
			if($moveto['id'] == Auth::user())
				die("Du får inte flytta biljetter till dig själv!");
			if(empty($moveto) || empty($_REQUEST['usertomoveto']))
			{
				ErrorHelper::error("Kan inte hitta användaren du vill flytta till!");
				return;
			}

			// For extra security, the user must reauthenticate.
			$mycurrentuser = Auth::user(true);
			if($user->auth($mycurrentuser['username'], @$_REQUEST['mypassword']) != $mycurrentuser['id']){
                                ErrorHelper::error("Du har skrivit in fel lösenord!");
                                return;

			}

			// Sanity check so that the user moves something:
			$movesomething = 0;
			foreach($_REQUEST['ammount'] as $ammount)
			{
				if($ammount > 0)
					$movesomething += $ammount;
			}
			
			if($movesomething <= 0)
			{
				ErrorHelper::error("Du måste flytta något!");
				return;
			}
			
			try
			{
				// log number of items to transfer and user stuff for future reference
				$logger->log("MoveInit", "Ticket transfer successfully initiated.", array("Totalt antal produkter att flytta" => $movesomething, "Mottagarens användar-ID" => $moveto['id'], "Mottagarens användarnamn" => $moveto['username']));
				
				// log source user's initial order status in a readable fashion
				$formatted_mashup = "";
				foreach($mashup as $key => $value)
					$formatted_mashup .= "\n{$value['name']} = {$value['ammount']}";
				$logger->log("MoveCurrentOrderAmount", "Computed order amounts.", array("Saldo" => $formatted_mashup));


				// Sanity check så man inte försöker flytta mer än man har.
				foreach($_REQUEST['ammount'] as $key => $ammount)
				{
					if($ammount > @$mashup[$key]['ammount'] || $ammount < 0)
					{
						$logger->log("MoveError", "Requested amount to transfer > amount available", array("Produkt" => $mashup[$key]['name'], "\$ammount" => $ammount, "\$mashup[$key]['ammount']" => print_r(@$mashup[$key]['ammount'], true)));
						die("Fuskfångst. Du försökte ge bort mer av en typ än du har.");
					}
				}

				
				// Skapa en order på användaren du tänker flytta på. Markera den som 'moved'.
				$order_id = $order->addOrder(array('user_id' => $moveto['id'],
				   'payson_token' => 'moved',
					'code_id' => 0
					));
				$logger->log("MoveAddOrderToUser", "New order created for recipient user", array("Ordernummer" => $order_id));

				// Markera ordern som betald.

				$order->setStatusById($order_id, 'COMPLETED');

				// Gå igenom allt vi vill flytta:

				foreach($_REQUEST['ammount'] as $key => $ammount) // ammount = Hur mycket man vill flytta. key = alternatividt
				{
					$mashup[$key]['ammount_moved'] = $ammount; // for later logging purposes
					if($ammount > 0) // Om vi försöker flytta något
					{
						$ammount_to_delete = $ammount;
						// Hämta ut alla saker av den typen från vår användare
						$deleteloop = $ordersvalues->getByUserIDAndAlternativeID(Auth::user(), $key);
						foreach($deleteloop as $del) // Loopa igenom dem.
						{
							if ($ammount_to_delete <= 0)
								break;
							$transfer_id = $logger->logTransfer(Auth::user(), $moveto['id'], $del['order_id'], $order_id, $del['order_alternative_id'], min($del['ammount'], $ammount_to_delete));
							
							if(($del['ammount'] - $ammount_to_delete) <= 0) // Om resterna blir under 0
							{
								$ordersvalues->delete($del['id']); // Ta bort ordersvaluen
								$ammount_to_delete = $ammount_to_delete - $del['ammount']; // Och kom ihåg hur mycket som finns kvar.
								$logger->log("MoveDeleteFromSourceUser", "Removing item from source user.", array("Ordernummer" => $del['order_id'], "Produkt" => $mashup[$del['order_alternative_id']]['name'], "Antal" => $del['ammount']));
							} else {
								if ($ammount_to_delete <= 0)
									break; // avoid unnecessary UPDATE
								$ordersvalues->updateammount($del['id'], $del['ammount'] - $ammount_to_delete); // Annars, uppdatera statusen.
								$logger->log("MoveReduceFromSourceUser", "Reducing ammount of owned item for source user.", array("Ordernummer" => $del['order_id'], "Produkt" => $mashup[$del['order_alternative_id']]['name'], "Antal" => $del['ammount']));
								/*	if($del['ammount'] - $ammount_to_delete == 0)
								{
									$ordersvalues->delete($del['id']);
								}*/
								$ammount_to_delete = 0;
								break;
							}
						}

						// Lägg till den hos mottagaren.
						$ordersvalues->addOrderValue(array('order_id' => $order_id, 'order_alternative_id' => $key, 'value' => @$mashup[$key]['value'], 'ammount' => $ammount));
						$logger->log("MoveAddOrderToRecipient", "New item successfully added to recipient user.", array("Ordernummer" => $order_id, "Produkt" => $mashup[$key]['name'], "Antal" => $ammount, "Transaktions-ID" => $transfer_id));
					}
				}

				foreach($myorders as $myorder) // Kolla om mina ordrar är tomma. Ta bort de som är tomma.
				{
					$the_ordersvalues = $ordersvalues->getOrderValuesFromOrder($myorder['id']);
					if(empty($the_ordersvalues))
					{
						$order->setStatusById($myorder['id'], 'MOVEEMPTY'); // MOVEEMPTY since 2012-06-12, before that NOTPAYED
						$logger->log("MoveRemoveEmptyOrder", "Successfully removed a completely empty order with id=" . $myorder['id']);
					}
				}
				
				$formatted_mashup = "";
				foreach($mashup as $key => $value)
					$formatted_mashup .= "\n{$value['name']} = " . ($value['ammount'] - @$value['ammount_moved']);
				$logger->log("MoveTransferComplete", "Order transfer successfully completed.", array("Nytt saldo" => $formatted_mashup));

				$this->_redirect('move_jump');
			} // end try
			catch(Exception $e)
			{
				$logger->log("MoveError", "Uncaught exception in ticket controller.", array("description" => $e->getMessage()));
			} // end catch
		}
	}

	public function move_jump()
	{
		ErrorHelper::success("Överföringen av produkt(erna) lyckades!");
		$this->view = 'ticket.index.php';
                        $this->index();
	}
	
	public function getticket()
	{
		if (!$this->_checkLogin()) 
			return;
		$order = Model::getModel('order');
		$member = Model::getModel('member');
		$ordersvalues = Model::getModel('ordersvalues');

		$themember = $member->getMemberByUserID(Auth::user()); // Get the member from the database corresponding to the user.
		$myorders = $order->getOrderFromUserAndStatus(Auth::user(), 'COMPLETED');
		
		if (!count($myorders)) {
			die("Du har ingen order, eller är inte inloggad. Vid frågor, kontakta kundtjanst@narcon.se");
		}

		$this->_buildAlternativeTreeNoFilter();

		$orderstring = "";
		$boughtticket = false;

		$mashup = array();
		foreach($myorders as $myorder)
		{
			$the_ordersvalues = $ordersvalues->getOrderValuesFromOrder($myorder['id']);
	                foreach ($the_ordersvalues as $value) {
				if(empty($mashup[$value['id']])) // Create an ordersvaluesarray with all the values combined.
				{
					$mashup[$value['id']] = $value;
				} else {
					$mashup[$value['id']]['ammount'] += $value['ammount']; 
				}

	                        if($value['id'] == 2 || $value['id'] == 33) {
	                                $boughtticket = true;
	                        }
	                }
		}

		foreach($mashup as $key => $ordervalue)
		{
	                        if($ordervalue['name'] != '') {
	                                $orderstring .= $ordervalue['ammount'] . ' x ' . ( @$this->tree_simple[$this->tree_simple[$key]['parent']]['template_override'] == 'select' ? $this->tree_simple[$this->tree_simple[$key]['parent']]['name'] . ' - ' . $ordervalue['name'] : $ordervalue['name']) . '   ' . $ordervalue['cost'] . 'kr' . "\r\n";
	                        } else {
	                                $orderstring .= "";
	                        }
		}
		
		$ticket = CFactory::getTicketGen($boughtticket ? '/tickettemplate.pdf' : '/kvittotemplate.pdf');

		$ticket->addBarCode(90, 275, $themember['PersonID'] . '-' . strtoupper( substr(hash('SHA512', $themember['PersonID'] . 
Settings::$BarKey), 0, 4 )), 0.5, 8);
		
		
		$ticket->_pdf->SetXY(90, 228);
		$ticket->_pdf->Cell(0,0, utf8_decode($themember['firstName'] . ' ' . $themember['lastName'] . ' ('. $themember['socialSecurityNumber'] . ')'));
		$ticket->_pdf->SetXY(30, 180);
		$ticket->_pdf->MultiCell(0, 5, utf8_decode($orderstring));
		$ticket->generate();
		exit();
	}
}
