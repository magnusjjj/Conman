<?php
/**
 *  This class does pretty much what is says on the label, it moves tickets and orderitems between users.
 */
class TicketMover
{
    /**
     * This function is pretty much a wrapper function to move, allowing you to use a username to move to,
     * instead of an user id.
     * @param $first_user_id        The user to move from
     * @param $second_user_name     The user to move to
     * @param $what                 What to move. Takes an indexed array of alternative -> how many to move.
     * @param bool $checkpassword   Whether or not to check the password of the moving user. Defaults to false.
     * @param bool $the_password    The password to check for. Defaults to empty string.
     */
    function movebyusername($first_user_id, $second_user_name, $what, $checkpassword = false, $the_password = false)
    {
        $user = Model::getModel('user');
        $moveto = $user->getByUsernameOrEmail($second_user_name);

        // Check if the user exists, and sanitycheck.
        if (empty($moveto) || empty($second_user_name)) {
            ErrorHelper::error("Kan inte hitta användaren du vill flytta till!");
            return;
        }

        return $this->move($first_user_id, $moveto['id'], $what, $checkpassword, $the_password);
    }

    /**
     * This function moves tickets, or items from a user to another user.
     * @param $fromid               The user to move from
     * @param $toid                 The user to move to
     * @param $what                 What to move. Takes an indexed array of alternative -> how many to move.
     * @param bool $checkpassword   Whether or not to check the password of the moving user. Defaults to false.
     * @param string $thepassword   The password to check for. Defaults to empty string.
     * @return bool                 Returns true if the transaction was successfull.
     */
    function move($fromid, $toid, $what, $checkpassword = false, $thepassword = '')
    {
        // Get all the database models to be used
        $order = Model::getModel('order');
        $ordersvalues = Model::getModel('ordersvalues');
        $user = Model::getModel('user');
        $logger = Model::getModel('log');

        // Get the ticket helper, to generate some helper variables with a list of names of things to buy, and
        // a mashup of all the users things.
        $tickethelper = CFactory::getTicketHelper();

        // Get the users to move from, and to
        $theuser = array_pop($user->get($fromid));
        $moveto = array_pop($user->get($toid));

        // Get all the users orders
        $myorders = $order->getOrderFromUserAndStatus($fromid, 'COMPLETED');

        // Create a mashup of all the users things. A mashup is an array of alternativeid -> how many the user has.
        $mashup = $tickethelper->createMashup($theuser['id']);

        // Create an array of alternative_id -> the alternative. Example $tree_simple[$id]['name'] is the alternative
        // name :)
        $tree_simple = $tickethelper->buildAlternativeTreeNoFilter();
        $tree_simple = $tree_simple['tree_simple'];

        // You can't move tickets to yourself
        if ($fromid == $toid) {
            die("Du får inte flytta biljetter till dig själv!");
        }

        // For extra security, the user must reauthenticate when moving tickets.
        if ($checkpassword) {
            if ($user->auth($theuser['username'], $thepassword) != $theuser['id']) {
                ErrorHelper::error("Du har skrivit in fel lösenord!");
                return;
            }
        }

        // Sanity check so that the user moves something:
        $movesomething = 0;
        foreach ($what as $amount) {
            if ($amount > 0)
                $movesomething += $amount;
            if ($amount < 0) {
                ErrorHelper::error("Du får inte flytta negativt med saker!");
                return;
            }
        }

        // The user has to move something
        if ($movesomething <= 0) {
            ErrorHelper::error("Du måste flytta något!");
            return;
        }

        try {

            // Log number of items to transfer and user stuff for future reference
            $logger->log("MoveInit", "Ticket transfer successfully initiated.", array("Sändarens id-nummer" => $theuser['id'], "Totalt antal produkter att flytta" => $movesomething, "Mottagarens användar-ID" => $moveto['id'], "Mottagarens användarnamn" => $moveto['username']));

            // Log source user's initial order status in a readable fashion
            $formatted_mashup = "";
            foreach ($mashup as $key => $value)
                $formatted_mashup .= "\n{$tree_simple[$key]['name']} = {$value}";
            $logger->log("MoveCurrentOrderAmount", "Computed order amounts.", array("Saldo" => $formatted_mashup));

            // Sanity check så man inte försöker flytta mer än man har.
            foreach ($what as $key => $amount) {
                if ($amount > @$mashup[$key] || $amount < 0) {
                    $logger->log("MoveError", "Requested amount to transfer > amount available", array("Produkt" => $tree_simple[$key]['name'], "\$ammount" => $amount, "\$tree_simple[$key]['ammount']" => print_r(@$tree_simple[$key]['ammount'], true)));
                    die("Fuskfångst. Du försökte ge bort mer av en typ än du har.");
                }
            }

            // TODO: payson_token ? NOOOOOOOOOOOOOOOOOOOOOOOOOO
            // Create a user on the the recipient, mark it as 'moved'.
            $order_id = $order->addOrder(array('user_id' => $moveto['id'],
                'payson_token' => 'moved',
                'code_id' => 0
            ));
            $logger->log("MoveAddOrderToUser", "New order created for recipient user", array("Ordernummer" => $order_id));

            // Mark the order as payed

            $order->setStatusById($order_id, 'COMPLETED');

            // Go through everything we want to move

            $movedarr = array();
            foreach ($what as $key => $amount) // amount = How much to move. key = the alternative id
            {
                $movedarr[$key] = $amount;
                if ($amount > 0) // If we are trying to move something
                {
                    $amount_to_delete = $amount;
                    // Fetch all items of that type from the user
                    $deleteloop = $ordersvalues->getByUserIDAndAlternativeID($theuser['id'], $key);
                    foreach ($deleteloop as $del) // Loop through them
                    {
                        if ($amount_to_delete <= 0) // If we are finished, stop
                            break;
                        // Create a new transfer id, to log these transactions.
                        $transfer_id = $logger->logTransfer($theuser['id'], $moveto['id'], $del['order_id'], $order_id, $del['order_alternative_id'], min($del['ammount'], $amount_to_delete));

                        if (($del['ammount'] - $amount_to_delete) <= 0) // If we are completely emptying an orders_value from an item
                        {
                            $ordersvalues->delete($del['id']); // Then delete that orders_value
                            $amount_to_delete = $amount_to_delete - $del['ammount']; // Remember how much remains
                            $logger->log("MoveDeleteFromSourceUser", "Removing item from source user.", array("Ordernummer" => $del['order_id'], "Produkt" => $tree_simple[$del['order_alternative_id']]['name'], "Antal" => $del['ammount']));
                        } else {
                            if ($amount_to_delete <= 0)
                                break; // avoid unnecessary UPDATE
                            // Update the order, remove as many of that item as necessary
                            $ordersvalues->updateammount($del['id'], $del['ammount'] - $amount_to_delete); // Annars, uppdatera statusen.
                            $logger->log("MoveReduceFromSourceUser", "Reducing ammount of owned item for source user.", array("Ordernummer" => $del['order_id'], "Produkt" => $tree_simple[$del['order_alternative_id']]['name'], "Antal" => $del['ammount']));
                            break;
                        }
                    }

                    // Add the item to the reciever
                    $ordersvalues->addOrderValue(array('order_id' => $order_id, 'order_alternative_id' => $key, 'value' => @$mashup[$key]['value'], 'ammount' => $amount));
                    $logger->log("MoveAddOrderToRecipient", "New item successfully added to recipient user.", array("Ordernummer" => $order_id, "Produkt" => $tree_simple[$key]['name'], "Antal" => $amount, "Transaktions-ID" => $transfer_id));
                }
            }

            foreach ($myorders as $myorder) // Change all empty orders to 'MOVEEMPTY'. That way we can keep track of payments.
            {
                $the_ordersvalues = $ordersvalues->getOrderValuesFromOrder($myorder['id']);
                if (empty($the_ordersvalues)) {
                    $order->setStatusById($myorder['id'], 'MOVEEMPTY');
                    $logger->log("MoveRemoveEmptyOrder", "Successfully removed a completely empty order with id=" . $myorder['id']);
                }
            }

            // Refresh mashup
            $mashup = $tickethelper->createMashup($theuser['id']);

            // Log some stuff, then return happy.
            $formatted_mashup = "";
            foreach ($mashup as $key => $value)
                $formatted_mashup .= "\n{$tree_simple[$key]['name']} = " . $mashup;
            $logger->log("MoveTransferComplete", "Order transfer successfully completed.", array("Nytt saldo" => $formatted_mashup));
            return true;
        } // end try
        catch (Exception $e) {
            $logger->log("MoveError", "Uncaught exception in ticket controller.", array("description" => $e->getMessage()));
        } // end catch
    }
}