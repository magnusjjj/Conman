<?php
class TicketController extends Controller
{
    // This is a helper thar rests in /includes/tickethelper.php.
    // It holds a couple of functions widely used in this file, namely:
    // - Two functions for generating trees with information about all the things that the user can buy.
    // - A function for checking if the users membership is in need of updating
    // - A function to mark an order as paymed.
    // - A function to check if a something is a ticket or not, and if a user has a ticket.
    var $_TicketHelp = null;

    /**
     * Constructor. Mostly just sets the _TicketHelp variable.
     */
    function __construct()
    {
        parent::__construct();
        $this->_TicketHelp = CFactory::getTicketHelper();
    }

    /**
     * Function for kicking out the user if its not logged in.
     * @return bool Returns false if the calling code should return.
     */
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

    /**
     * The index page. Displayed when the user has logged in.
     * If the user has no payed order, this redirects directly to 'buystuff'
     */
    public function index()
    {
        if (!$this->_checkLogin()) // Kick the user if its not logged in.
            return;

        // Get all the database models used in this function
        $order = Model::getModel('order');
        $ordersvalues = Model::getModel('ordersvalues');

        $myorders = $order->getOrderFromUserAndStatus(Auth::user(), 'COMPLETED'); // Get all the users payed orders.

        if (count($myorders)) { // If the user has payed orders.
            // Todo: Check if ticket.index.php is ever used
            // Choose a different view
            $this->view = 'ticket.hasticket.php';

            // Prepare the view. We send it some information about what the user has bought, to aid in the order list.
            // The variable ordersvalues will contain things the user has bought, grouped by order.
            $ordersvalues_complete = array();
            foreach ($myorders as $key => $the_order) {
                $ordersvalues_complete[$key] = $ordersvalues->getOrderValuesFromOrder($the_order['id']);
            }

            $this->_set('orders', $myorders); // All of the orders
            $this->_set('boughtticket', $this->_TicketHelp->has_ticket(Auth::user())); // If the user has bought a ticket
            $this->_set('ordersvalues', $ordersvalues_complete);

            // To aid in the listing, we give the view a list of all the order alternatives.
            $tree = $this->_TicketHelp->buildAlternativeTree();
            $this->_set('alternatives_simple', $tree['tree_simple']);
        } else {
            // Redirect the user, so that zhe can buy stuff.
            $this->_redirect('buystuff');
        }
    }

    /**
     * The page for showing the user all the nice things zhe can buy
     */
    function buystuff()
    {
        if (!$this->_checkLogin()) // Kick the user if its not logged in.
            return;

        // Check if the user is a payed up member:
        $member = Model::getModel('member');
        $themember = $member->getMemberByUserID(Auth::user());
        $this->_set('is_member', $this->_TicketHelp->checkMembership($themember));

        // Fetch the order alternatives, so that we can print a list of all the nice things the user can buy
        $tree = $this->_TicketHelp->buildAlternativeTree();
        $this->_set('alternatives_parents', $tree['tree_parents']);
        $this->_set('alternatives_children', $tree['tree_children']);
    }

    /**
     * Show the user the default payment information page. Its a static info page.
     */
    function buystuff_info()
    {
        if (!$this->_checkLogin()) // Kick the user if its not logged in.
            return;
    }

    /**
     * This is one mammoth of a function, and handles all the processing on what to do when a user wants
     * to place an order.
     * It takes a couple of inputs:
     * $_REQUEST['code']        Either a pre-order code, or a discount code.
     * $_REQUEST['iaccept']     Whether or not the user has accepted the payment agreement.
     * $_REQUEST['val']         An associative array, with the alternative id being the index, and, depending on what
     * override is being used.
     *  - If the value is numeric, then its a select box, or a alternative with children.
     *    It will use the child as the chosen product .
     *  - If the value is an array, its an override with a special handler.
     *    It then opens itemtypes/overrides/(name of the override).php to handle that product.
     *  - If there is no real value, then its a checkbox or on/off thing.
     * $_REQUEST['ammount']     An associative array, with the alternative id being the index, and the ammount the user
     * wants to buy the value.
     *
     *
     * Things to test after messing about:
     * - Create a code and use it, both more and less than what you try to buy.
     * - Check so that you cant use the same code again.
     * - Try to buy more than one thing at a time.
     * - Try it with both a payment provider, and without it.
     */

    public function gotopay()
    {
        if (!$this->_checkLogin())
            return;
        // Import the payson api
        require_once(Settings::getRoot() . '/includes/payapi/' . Settings::$PayAPI['Name'] . 'Provider.php');
        // Get the models
        $alternatives = Model::getModel('ordersalternatives');
        $order = Model::getModel('order');
        $member = Model::getModel('member');
        $ordervalues = Model::getModel('ordersvalues');
        $payapisave = Model::getModel('payapisave');
        $orderscodes = Model::getModel('orderscodes');

        // Get the different values, used later
        $themember = $member->getMemberByUserID(Auth::user()); // Get the member from the database corresponding to the user.
        $cost = 0; // This will contain the total cost
        $stuff = array(); // This will contain the payson OrderItem's.

        // Check so that the user has accepted the payment agreement.
        if (empty($_REQUEST['iaccept'])) {
            ErrorHelper::error('Du måste acceptera köpvillkoren!');
            $this->view = 'ticket.buystuff.php';
            $this->buystuff();
            return;
        }

        // TODO: This is useful commented-out functionality. It makes it obligatory to have a pre-order code.
        /*if(empty($_REQUEST['code']))
        {
                        $this->_set('error', 'Du måste ha en kod under förköpet!');
                        $this->view = 'ticket.buystuff.php';
                        $this->buystuff();
                        return;
        }*/


        // Below we check if the user has an order, or preorder code they would like to use.
        $code_id = 0;
        $code_reduction = 0;

        if (!empty($_REQUEST['code'])) {
            $the_code = $orderscodes->getCode($_REQUEST['code']); // Fetch the code
            if (empty($the_code)) { // If the result was empty, we abort.
                ErrorHelper::error('Den koden finns inte!');
                $this->view = 'ticket.buystuff.php';
                $this->buystuff();
                return;
            } else { // If there result was not empty, we have a code
                if ($the_code['used_by']) { // Check so that the code is not already used
                    ErrorHelper::error('Den koden är redan använd!');
                    $this->view = 'ticket.buystuff.php';
                    $this->buystuff();
                    return;
                }
                // Set variables, so that we remember the code id, and the payment reduction for later.
                $code_id = $the_code['id'];
                $code_reduction = $the_code['reduction'];
            }
        }


        $thingstobuy = @$_REQUEST['val']; // This will be the array with the things the user has selected to buy
        $numbuy = @$_REQUEST['ammount']; // This will be an array which will specify how many of something the user wants to buy

        if (!array_sum($numbuy) || empty($thingstobuy)) { // The user has to buy something, so check that
            ErrorHelper::error('Du har inte markerat något du vill köpa!');
            $this->view = 'ticket.buystuff.php';
            $this->buystuff();
            return;
        }

        //TODO use buildAlternativeTree?
        // Build a tree of alternatives, for use when trying to add order items.
        $tree = $this->_TicketHelp->buildAlternativeTreeNoFilter();
        $tree_all = $tree['tree_simple']; // Indexed by the alternative id's

        // Rather complicated loop, to loop through all the things the user wants to buy and then
        // use different approaches to figure out how much everything costs, and what to save.
        try {
            foreach ($thingstobuy as $key => $thing) {
                if (@$numbuy[$key] > 0) {
                    if (is_numeric($thing)) { // If its a select-box, we get the price of the option
                        $cost += $numbuy[$key] * $tree_all[$thing]['cost']; // Add the cost
                        $stuff[] = array('Description' => $tree_all[$thing]['name'],
                            'SKU' => str_pad($key, 6, '0', STR_PAD_LEFT),
                            'Quantity' => $numbuy[$key],
                            'Price' => $tree_all[$thing]['cost'],
                            'Tax' => '0');
                    } else if (is_array($thing)) { // Its not a default action, like a checkbox or a select, but an override
                        include_once(dirname(__FILE__) . '/itemtypes/overrides/' . $tree_all[$key]['template_override'] . '.php'); // Include the override
                        $classname = ucfirst($tree_all[$key]['template_override']) . 'OrderItem'; // Create the override classname
                        $itemclass = new $classname($key, $thing, $tree_all); // Init the classname with the id, the object parameters, and the tree
                        while (($i = $itemclass->getItem()) !== null) { // Parse the results
                            $cost += $i['cost'] * $i['number']; // Add the cost
                            $stuff[] = array('Description' => $i['name'],
                                'SKU' => str_pad($key, 6, '0', STR_PAD_LEFT),
                                'Quantity' => $i['number'],
                                'Price' => $i['cost'],
                                'Tax' => '0');
                        }
                    } else if ($thing != 'NULL') { // Otherwise its a checkbox, get its stuff here
                        $cost += $numbuy[$key] * $tree_all[$key]['cost']; // Add the cost
                        $stuff[] = array('Description' => $tree_all[$key]['name'],
                            'SKU' => str_pad($key, 6, '0', STR_PAD_LEFT),
                            'Quantity' => $numbuy[$key],
                            'Price' => $tree_all[$key]['cost'],
                            'Tax' => '0');
                    } else { // Something is wonky. Remove that thing from the order.
                        unset($thingstobuy[$key]);
                        unset($numbuy[$key]);
                        if (empty($thingstobuy)) { // You will, of course, have to buy something
                            ErrorHelper::error('Du måste köpa något!');
                            $this->view = 'ticket.buystuff.php';
                            $this->buystuff();
                            return;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            ErrorHelper::error($e->getMessage()); // Catch errors from the overrides
            $this->view = 'ticket.buystuff.php';
            $this->buystuff();
            return;
        }

        // TODO This is actually useful commented out code. Adds the member cost to the order.
        if (!$this->_TicketHelp->checkMembership($themember)) { // Check if the person is a payed-up member or not.
            //$cost += Settings::$MembershipCost; // Else, add the membership cost
            //$stuff[] = new OrderItem('Medlemskap i föreningen', Settings::$MembershipCost, 1, 0, str_pad('80085', 6, '0', STR_PAD_LEFT)); // Create the payson Order-item
        }

        if ($code_reduction) { // If we have an discount code, we need to add that to the order list.
            $cost -= $code_reduction;
            $stuff[] = array('Description' => 'Kodrabatt',
                'SKU' => '80086',
                'Quantity' => 1,
                'Price' => 0 - $code_reduction,
                'Tax' => '0');
        }

        /* Now we set up the Payment Api, and feed it with the information in the Config */
        $apiname = Settings::$PayAPI['Name'] . 'Provider';
        $payapi = new $apiname(Settings::$PayAPI, Settings::$PayAPI['Test']);

        // Now we fill up a request with information about what things the user wants to buy, etc.
        $payapiarray = array('SenderEmail' => $themember['eMail'],
            'SenderFirstName' => $themember['firstName'], // First name
            'SenderLastName' => $themember['lastName'], // Last name
            'Description' => "Biljett", // What to call the transaction
            'Guarantee' => 'NO', // If any special arrangements with special modes should be used
            'FeePayer' => 'SENDER', // Who pays for the transaction cost?
            'Recievers' => array(array('Email' => Settings::$PayAPI['_application_email'], 'Amount' => $cost)), // Who should we send it to?
            'URLReturn' => Router::url('pay_return', true), // Where to return to
            'URLCancel' => Router::url('pay_cancel', true), // Where to return to if the user cancels.
            'URLIPN' => Router::url('pay_status', true), // Where to send payment notifications to
            'Items' => $stuff // What things to buy
        );

        // Then we send the payment request
        $payapiResponse = $payapi->InitializePayment($payapiarray);

        // We save the response for later.
        $saver = array('user_id' => Auth::user(), 'order_id' => 0, 'extref_id' => $payapiResponse['ExternalId'],
            'extref_other' => $payapiResponse['ExternalReference'],
            'originalresponse' => serialize($payapiResponse['Save']), 'status' => $payapiResponse['Status'],
            'type' => 'INIT'
        );
        $payapi_id = $payapisave->save($saver);

        if ($cost <= 0 || empty(Settings::$PayAPI)
            || $payapiResponse['Status'] == 'SUCCESS'
        ) { // If payson is dissallowed, or the payson order call was successful, we create the actual order in the database
            $order_id = $order->addOrder(array('user_id' => Auth::user(),
                'code_id' => $code_id
            ));

            // Now we update the payapi save with the order number.
            $payapisave->updateWithOrderId($payapi_id, $order_id);

            // This is pretty much a mirror of the main Payment lookup loop a bit further up in the loop
            // It creates order-orderalternative relationships in the database.
            foreach ($thingstobuy as $key => $thing) {
                if (@$numbuy[$key] != 0) {
                    $id = null;
                    $value = null;

                    if (is_numeric($thing)) { // If its a select-box, we use the value as the id
                        $id = $thing;
                        $value = '';
                    } else if (is_array($thing)) { // Its not a default action, like a checkbox or a select, but an override
                        $id = $key;
                        $value = serialize($thing);
                    } else { // Otherwise its a checkbox, get its stuff here
                        $id = $key;
                        $value = '';
                    }
                    // Add the values to the database.
                    $ordervalues->addOrderValue(array('order_id' => $order_id, 'order_alternative_id' => $id, 'value' => $value, 'ammount' => $numbuy[$key]));
                }
            }

            //TODO: Commented out usefull functionality. This checks if the membership is outdated.
            if (!$this->_TicketHelp->checkMembership($themember)) {
                //$ordervalues->addOrderValue(array('order_id' => $order_id, 'order_alternative_id' => 0, 'value' => 'MEMBERSHIP'));
            }

            // If the cost is equal, or less than zero, then the order is complete. No need to do anything more.
            if ($cost <= 0) {
                // Redirect to index.
                $this->view = 'ticket.index.php';
                $this->_TicketHelp->doCompleteOrder($order_id);
                $this->index();
                return;
            }

            // Now, if a PayAPI provider is activated, we need to send the user to the redirect adress.
            if (!empty(Settings::$PayAPI)) {
                // If we allow payson, redirect the user there
                header("Location: " . $payapiResponse['Redirect']);
                $this->_set('link', $payapiResponse['Redirect']); // Link for people without auto redirect. Grumble grumble.
            } else {
                // Else, the system is set in the 'local' mode, where orders need to be marked as payed in the entrance system.
                $order->setStatusById($order_id, 'MANUALNCOMPLETED');
                $this->_set('order_id', $order_id);
                $this->_set('member', $themember);
            }

        } else {
            ErrorHelper::error("Något gick fel med vår kontakt till betalleverantören. Kontakta oss för mer information!");
            //TODO: Log the error somewhere nice, or handle it in some way.
//			var_dump($payapiResponse);
        }
    }

    /**
     * This page is called when the user returns from a Payapi payment.
     * We perform a quick lookup to see what the status was.
     */
    public function pay_return()
    {
        // Create the Payapi object:
        require_once(Settings::getRoot() . '/includes/payapi/' . Settings::$PayAPI['Name'] . 'Provider.php');
        $apiname = Settings::$PayAPI['Name'] . 'Provider';
        $payapi = new $apiname(Settings::$PayAPI, Settings::$PayAPI['Test']);

        // Used so we can look up the order:
        $payapisave = Model::getModel('payapisave');

        // Call the handler.
        $payapiResponse = $payapi->handleReturn();

        // We are going to log this event
        $saver = array('extref_id' => $payapiResponse['ExternalId'], 'status' => $payapiResponse['Status'],
            'originalresponse' => serialize($payapiResponse['OriginalResponse']), 'type' => 'RETURN'
        );

        // Be pessimistic, assume that the transaction was a failure
        $apisave = array();
        $status = 'FAILURE';

        if ($payapiResponse['GetBy'] == 'ExternalReference') {
            // Get the save for that payment
            $apisave = $payapisave->getByExternalReference($payapiResponse['GetValue']);
            $apisave = @$apisave[0];
            // Fetch some data from the last save
            $saver['extref_other'] = $payapiResponse['GetValue'];
        }

        if (!empty($apisave)) {
            if ($payapiResponse['Status'] == 'COMPLETED') { // If the order was completed succcessfully
                $this->_TicketHelp->doCompleteOrder($apisave['order_id']); // Mark the order as payed
                $status = 'COMPLETED';
                // Fetch some data from the last save
                $saver['order_id'] = $apisave['order_id'];
                $saver['user_id'] = $apisave['user_id'];
            }
        }

        // Log it! Log it like a madman!
        $payapisave->save($saver);

        // Set the status in the view.
        $this->_set('status', $status);
        $this->view = 'ticket.index.php';
        $this->index();
    }

    /**
     * This page is viewed if a payapi order was cancelled.
     */
    public function pay_cancel()
    {
        $this->_set('error', 'Ditt köp blev avbrutet. Försök igen.');
        $this->view = 'ticket.buystuff.php';
        $this->buystuff();
    }

    /**
     * This page is viewed when the Payapi has a payment update.
     */

    public function pay_status()
    {
        // Create the Payapi object:
        require_once(Settings::getRoot() . '/includes/payapi/' . Settings::$PayAPI['Name'] . 'Provider.php');
        $apiname = Settings::$PayAPI['Name'] . 'Provider';
        $payapi = new $apiname(Settings::$PayAPI, Settings::$PayAPI['Test']);

        // Used so we can look up the order:
        $payapisave = Model::getModel('payapisave');

        // Call the handler for the payment update.
        $payapiResponse = $payapi->handleIPN();

        // We are going to log this event
        $saver = array('status' => $payapiResponse['Status'],
            'originalresponse' => serialize($payapiResponse['OriginalResponse']), 'type' => 'UPDATE'
        );

        $apisave = array();

        if ($payapiResponse['GetBy'] == 'ExternalReference') { // Now we look and see if we can find the order
            $apisave = $payapisave->getByExternalReference($payapiResponse['GetValue']);
            $apisave = @$apisave[0];
            // Get some data from the last save
            $saver['extref_other'] = $payapiResponse['GetValue'];
        }

        if (!empty($apisave)) {
            if ($payapiResponse['Status'] == 'COMPLETED') { // If the order was completed, set its status to completed.
                $this->_TicketHelp->doCompleteOrder($apisave['order_id']);
                // Get some data from the last save
                $saver['order_id'] = $apisave['order_id'];
                $saver['user_id'] = $apisave['user_id'];
                $saver['extref_id'] = $apisave['extref_id'];
            }
        }

        // Log it! Log it like a madman!
        $payapisave->save($saver);
    }

    /**
     * This page is called when the user wants to move tickets.
     */

    public function move()
    {

        if (!$this->_checkLogin()) // Am i logged in?
            return;

        $ticketmover = CFactory::getTicketMover();

        // Import models
        $order = Model::getModel('order');
        $ordersvalues = Model::getModel('ordersvalues');

        // Get a list of the orders that are completed.
        $myorders = $order->getOrderFromUserAndStatus(Auth::user(), 'COMPLETED');

        // If we can't find any orders, display an error:
        if (!count($myorders)) {
            ErrorHelper::error("Du har ingen order, eller är inte inloggad. Vid frågor, kontakta " . Settings::$CustomerserviceEmail);
            return;
        }

        // Get a list of all the different order alternatives
        $tree = $this->_TicketHelp->buildAlternativeTreeNoFilter();
        $this->tree_parents = $tree['tree_parents'];
        $this->tree_children = $tree['tree_children'];
        $this->tree_simple = $tree['tree_simple'];
        // We are going to store a list with all the ordersvalues combined in this, with all the values combined.
        $mashup = array();

        foreach ($myorders as $myorder) {
            $the_ordersvalues = $ordersvalues->getOrderValuesFromOrder($myorder['id']);
            foreach ($the_ordersvalues as $value) {
                if ($value['given'] == 0) {
                    if (empty($mashup[$value['id']])) // Create an ordersvaluesarray with all the values combined.
                    {
                        $mashup[$value['id']] = $value;
                    } else {
                        $mashup[$value['id']]['ammount'] += $value['ammount'];
                    }
                }
            }
        }


        // Send it to the view
        $this->_set('ordersvalues', $mashup);
        $this->_set('tree_simple', $this->tree_simple);

        // If the user has posted a request for moving of a ticket..
        if (!empty($_REQUEST['ammount'])) {

            if($ticketmover->movebyusername(Auth::user(), @$_REQUEST['usertomoveto'], $_REQUEST['ammount'], true, @$_REQUEST['mypassword']))
                $this->_redirect('move_jump');
        }

    }

    /**
     * This page is called when the user has moved tickets, to make sure they don't accidentaly refresh.
     */
    public function move_jump()
    {
        ErrorHelper::success("Överföringen av produkt(erna) lyckades!");
        $this->view = 'ticket.index.php';
        $this->index();
    }

    // TODO: Maybee move this to a seperate file? It can, and it would allow us to do more interesting things
    // TODO: Without cludging up more of this file
    /**
     * This page is called when the PDF file for the ticket should be generated.
     */
    public function getticket()
    {
        if (!$this->_checkLogin())
            return;

        // Get all the database models we need.
        $order = Model::getModel('order');
        $member = Model::getModel('member');
        $ordersvalues = Model::getModel('ordersvalues');

        // Get the member from the user, used to print out names and such.
        $themember = $member->getMemberByUserID(Auth::user()); // Get the member from the database corresponding to the user.
        $myorders = $order->getOrderFromUserAndStatus(Auth::user(), 'COMPLETED');

        // Check so that the user actually has any orders.
        if (!count($myorders)) {
            die("Du har ingen order, eller är inte inloggad. Vid frågor, kontakta " . Settings::$CustomerserviceEmail);
        }

        // Build the alternative tree, so we can print out names of order items :)
        $tree = $this->_TicketHelp->buildAlternativeTreeNoFilter();
        $this->tree_parents = $tree['tree_parents'];
        $this->tree_children = $tree['tree_children'];
        $this->tree_simple = $tree['tree_simple'];
        $orderstring = "";
        $boughtticket = $this->_TicketHelp->has_ticket(Auth::user());

        // Get a list of order items, and how much we have of them
        $mashup = $this->_TicketHelp->createMashup(Auth::user());

        foreach ($mashup as $key => $ordervalue) { // Loop through them, to generate a string with a list of items.
            // Simplify generating the string a bit, by picking out the products.
            $product = @$this->tree_simple[$key];
            $parent = @$this->tree_simple[$product['parent']];
            // Generate the string. If its a select, we want to have the parents name in there.
            //TODO: Generalise this, so that other overrides can use it.
            $orderstring .= $ordervalue . ' x ' .
            (@$parent['template_override'] == 'select' ? $parent['name'] . ' - ' . $product['name'] : $product['name']) . '   ' . $product['cost'] . 'kr' . "\r\n";
        }

        // Get the pdf generator
        $ticket = CFactory::getTicketGen($boughtticket ? '/tickettemplate.pdf' : '/kvittotemplate.pdf');

        // Add the barcode to the pdf
        $ticket->addBarCode(90, 275, $themember['PersonID'] . '-' . strtoupper(substr(hash('SHA512', $themember['PersonID'] .
            Settings::$BarKey), 0, 4)), 0.5, 8);

        // Put some information about the members, and the list of items
        $ticket->_pdf->SetXY(90, 228);
        $ticket->_pdf->Cell(0, 0, utf8_decode($themember['firstName'] . ' ' . $themember['lastName'] . ' (' . $themember['socialSecurityNumber'] . ')'));
        $ticket->_pdf->SetXY(30, 180);
        $ticket->_pdf->MultiCell(0, 5, utf8_decode($orderstring));
        $ticket->generate();
        // We do not want to print the html template ;)
        exit();
    }
}
