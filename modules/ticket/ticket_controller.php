<?php
class TicketController extends Controller 
{
	private function _checkLogin()
	{
		if (!Auth::user()) {
			$this->_redirect('/index/kicked');
			ErrorHelper::error('Du är utloggad!');
			return false;
		} else {
			return true;
		}
	}
	
	private function _buildAlternativeTree()
	{
		$alternatives = Model::getModel('ordersalternatives');
		$the_alternatives = $alternatives->getAlternatives();
		$tree_parents = array();
		$tree_children = array();
		$tree_simple = array();
		foreach ($the_alternatives as $alternative) {
			if ($alternative['ammount_compare'] >= $alternative['ammount'] 
				&& $alternative['ammount'] != 0
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

	private function _checkMembership($member)
	{
		$memdate = strtotime($member['membershipEnds']);
		$sysdate = strtotime(Settings::$ConEnds);
		if ($memdate < $sysdate)
			return false;
		else
			return true;
	}

	public function index()
	{
		if (!$this->_checkLogin()) 
			return;
		$order = Model::getModel('order');
		$myorder = $order->getOrderFromUserAndStatus(Auth::user(), 'COMPLETED');
		
		if (count($myorder)) {
			$this->view = 'ticket.hasticket.php';
			$this->_buildAlternativeTree();
			$ordersvalues = Model::getModel('ordersvalues');
			$this->_set('ordersvalues', $ordersvalues->getOrderValuesFromOrder($myorder[0]['id']));
			$this->_set('alternatives_simple', $this->tree_simple);
		} else {
			$this->_buildAlternativeTree();
			$member = Model::getModel('member');
			$themember = $member->getMemberByUserID(Auth::user());

			$this->_set('is_member', $this->_checkMembership($themember));
			$this->_set('alternatives_parents', $this->tree_parents);
			$this->_set('alternatives_children', $this->tree_children);
		}
	}
	
	public function gotopay()
	{
		if (!$this->_checkLogin()) 
			return;
		// Import the payson api
		require_once(Settings::getRoot() . '/includes/paysonapi/lib/paysonapi.php');
		// Get the models
		$alternatives = Model::getModel('ordersalternatives');
		$the_alternatives = $alternatives->getAlternatives();
		$order = Model::getModel('order');
		$member = Model::getModel('member');
		$ordervalues = Model::getModel('ordersvalues');
		$themember = $member->getMemberByUserID(Auth::user()); // Get the member from the database corresponding to the user.
		
		
		$thingstobuy = $_REQUEST['val']; // This will be the array with the things the user has selected to buy
		
		if (empty($thingstobuy)) { // You will, of course, have to buy something
			$this->_set('error', 'Du måste köpa något!'); 
			$this->view = 'ticket.index.php';
			$this->index();
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
				if (is_numeric($thing)) { // If its a select-box, we get the price of the option
					$cost += $tree_all[$thing]['cost']; // Add the cost
					$stuff[] = new OrderItem($tree_all[$thing]['name'], $tree_all[$thing]['cost'], 1, 0, str_pad($key, 6, '0', STR_PAD_LEFT)); // Create the payson order object
				} else if (is_array($thing)) {  // Its not a default action, like a checkbox or a select, but an override
					include_once(dirname(__FILE__) . '/itemtypes/overrides/'.$tree_all[$key]['template_override'].'.php'); // Include the override
					$classname = ucfirst($tree_all[$key]['template_override']) . 'OrderItem'; // Create the override classname
					$itemclass = new $classname($key, $thing, $tree_all); // Init the classname with the id, the object parameters, and the tree
					while (($i = $itemclass->getItem()) !== null) { // Parse the results
						$cost += $i['cost'] * $i['number']; // Add the cost
						$stuff[] = new OrderItem($i['name'], $i['cost'], $i['number'], 0, str_pad($key, 6, '0', STR_PAD_LEFT)); // Create the payson order object
					}
				} else if ($thing != 'NULL') { // Otherwise its a checkbox, get its stuff here
					$cost += $tree_all[$key]['cost']; // Add the cost
					$stuff[] = new OrderItem($tree_all[$key]['name'], $tree_all[$key]['cost'], 1, 0, str_pad($key, 6, '0', STR_PAD_LEFT)); // Create the payson order object
				}
			}
		} catch (Exception $e) {
			$this->_set('error', $e->getMessage()); // Catch errors from the overrides
			$this->view = 'ticket.index.php';
			$this->index();
			return;
		}
		
		
		if (!$this->_checkMembership($themember)) { // Check if the person is a payed-up member or not.
			$cost += Settings::$MembershipCost; // Else, add the membership cost
			$stuff[] = new OrderItem('Medlemskap i föreningen', Settings::$MembershipCost, 1, 0, str_pad('80085', 6, '0', STR_PAD_LEFT)); // Create the payson Order-item
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
		$payResponse = $api->pay($payData);

		if ((isset(Settings::$AllowPayson) && !Settings::$AllowPayson) 
			|| $payResponse->getResponseEnvelope()->wasSuccessful()
		) { // If payson is dissallowed, or the payson order call was successfull, we create the actuall order in the database
			$order_id = $order->addOrder(array('user_id' => Auth::user(), 'payson_token' => (isset(Settings::$AllowPayson) && !Settings::$AllowPayson) ? '' : $payResponse->getToken()));
			foreach ($thingstobuy as $key => $thing) {
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
				$ordervalues->addOrderValue(array('order_id' => $order_id, 'order_alternative_id' => $id, 'value' => $value));
			}
			
			if (!( isset(Settings::$AllowPayson) && !Settings::$AllowPayson )) { // If we allow payson, redirect the user there
				header("Location: " . $api->getForwardPayUrl($payResponse)); // If we allow payson, redirect the user there
				$this->_set('link',  $api->getForwardPayUrl($payResponse)); // Link for people without auto redirect. Grumble grumble.
			} else {
				$order->setStatusById($order_id, 'MANUALNCOMPLETED');
				$this->_set('order_id', $order_id);
			}
		} else {
			ErrorHelper::error("Något gick fel med vår kontakt till Payson");
		}
	}
	
	public function pay_return()
	{
		if(!$this->_checkLogin()) 
			return;
		require_once(Settings::getRoot() . '/includes/paysonapi/lib/paysonapi.php');
		$credentials = new PaysonCredentials(Settings::$PaysonAgentID, Settings::$PaysonMD5);
		$api = new PaysonApi($credentials);
		$order = Model::getModel('order');
		$paymentDetailsData = new PaymentDetailsData($_REQUEST['TOKEN']);
		$paymentDetailsResponse = $api->paymentDetails($paymentDetailsData);
		$paydetails = $paymentDetailsResponse->getPaymentDetails();
		if ($paydetails->getStatus() == 'COMPLETED') {
			$order->setStatus($paydetails->getToken(), $paydetails->getStatus());
		}
		$this->_set('status', $paydetails->getStatus());
		$this->view = 'ticket.index.php';
		$this->index();
	}
	
	public function pay_cancel()
	{
		if (!$this->_checkLogin()) 
			return;
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
	
	public function getticket()
	{
		if (!$this->_checkLogin()) 
			return;
		$order = Model::getModel('order');
		$member = Model::getModel('member');
		$themember = $member->getMemberByUserID(Auth::user()); // Get the member from the database corresponding to the user.
		$myorder = $order->getOrderFromUserAndStatus(Auth::user(), 'COMPLETED');
		
		if (!count($myorder)) {
			die("Du har ingen order, eller är inte inloggad.");
		}
		
		$ticket = CFactory::getTicketGen();
		$this->_buildAlternativeTree();
		$ordersvalues = Model::getModel('ordersvalues');
		$the_ordersvalues = $ordersvalues->getOrderValuesFromOrder($myorder[0]['id']);
		echo mysql_error();
		// Dirty Hikari-Con-loop, not proud monkey
		$ticket->addBarCode(150, 230, $themember['socialSecurityNumber'], 0.5, 8);
		/*$sovsal = false;
		foreach ($the_ordersvalues as $key => $value) {
			switch ($value['order_alternative_id']) {
				case 6:
					$sovsal = true;
					break;
				default:
					break;
			}
		}
		* 
		if ($sovsal) {
			$ticket->pdf->SetXY(55, 220);
			$ticket->pdf->Cell(0,0, 'X');
		} else {
			$ticket->pdf->SetXY(75, 220);
			$ticket->pdf->Cell(0,0, 'X');
		}*/
		
		$ticket->pdf->SetXY(55, 63);
		$ticket->pdf->Cell(0,0, utf8_decode($themember['firstName']));
		$ticket->pdf->SetXY(55, 69.5);
		$ticket->pdf->Cell(0,0,utf8_decode($themember['lastName']));
		$ticket->pdf->SetXY(55, 76);
		$ticket->pdf->Cell(0,0,$themember['socialSecurityNumber']);
		$ticket->pdf->SetXY(105, 69);
		$ticket->pdf->Cell(0,0,utf8_decode($themember['streetAddress']));
		$ticket->pdf->SetXY(105, 76);
		$ticket->pdf->Cell(0,0, $themember['zipCode'] . ' ' . utf8_decode($themember['city']));
		$ticket->pdf->SetXY(30, 80);
		$orderstring = "";
		foreach ($the_ordersvalues as $value) {
			$orderstring .= $value['name'] . '   ' . $value['cost'] . 'kr' . "\r\n";
		}
		$ticket->pdf->MultiCell(0, 20, utf8_decode($orderstring));
		$ticket->generate();
		exit();
	}
}
