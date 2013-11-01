<?php
    class TicketHelper {

        /**
         * This function creates a mashup, an array that explains how many of each orderalternative the user has,
         * regardless of which order they are in.
         * @param $user_id  The user to create a mashup for.
         * @return array    An indexed array, alternative_id -> how many the user has
         */
        public function createMashup($user_id)
        {
            $mashup = array();
            $order = Model::getModel('order');
            $ordersvalues = Model::getModel('ordersvalues');

            // Get all the payed orders
            $myorders = $order->getOrderFromUserAndStatus($user_id, 'COMPLETED');

            foreach ($myorders as $myorder) { // Loop through all the orders
                // Get all the values from the orders
                $the_ordersvalues = $ordersvalues->getOrderValuesFromOrder($myorder['id']);
                foreach ($the_ordersvalues as $value) { // And then loop through them
                    if ($value['given'] == 0) { // If the item has been given to the user, dont count it
                        if($value['ammount'] > 0)
                        {
                            if(empty($mashup[$value['id']]))
                                $mashup[$value['id']] = $value['ammount'];
                            else
                                $mashup[$value['id']] += $value['ammount'];
                        }
                    }
                }
            }
            return $mashup;
        }

        public function buildAlternativeTree() // Function used for building a tree of different ticket types POORLYCOMMENTED
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
            return compact('tree_parents', 'tree_children', 'tree_simple');
        }

        public function buildAlternativeTreeNoFilter() // Function used for building a tree of different ticket types POORLYCOMMENTED
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
            return compact('tree_parents', 'tree_children', 'tree_simple');
        }

        public function checkMembership($member) // Used for checking if the users membership is outdated. POORLYCOMMENTED, SHOULDBEMOVED
        {
            $memdate = strtotime($member['membershipEnds']);
            $sysdate = strtotime(Settings::$ConEnds);
            if ($memdate < $sysdate)
                return false;
            else
                return true;
        }

        public function doCompleteOrder($orderid)
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

        function is_ticket($alternative_id)
        {
            //TODO fill this with actuall code!
            if ($alternative_id == 2 || $alternative_id == 33 || $alternative_id == 40 || $alternative_id== 41 || $alternative_id == 42) {
                return true;
            } else {
                return false;
            }
        }

        function has_ticket($user_id)
        {
            $order = Model::getModel('order');
            $ordersvalues = Model::getModel('ordersvalues');
            $myorders = $order->getOrderFromUserAndStatus(Auth::user(), 'COMPLETED'); // Get all the users payed orders.

            $boughtticket = false;
            foreach ($myorders as $myorder) {
                $the_ordersvalues = $ordersvalues->getOrderValuesFromOrder($myorder['id']);
                foreach ($the_ordersvalues as $value) {
                    if($this->is_ticket($value['id']))
                    {
                        $boughtticket = true;
                    }

                }
            }
            return $boughtticket;
        }
    }