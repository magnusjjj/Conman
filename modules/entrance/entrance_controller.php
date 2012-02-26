<?php
class EntranceController extends Controller 
{
	public function __construct()
	{
		parent::__construct();
		Settings::$Template = 'ajax';
		if(!$this->_checkLogin()) 
			return;
	}
	
	private function _checkLogin()
	{
		$user = Auth::user(true);
		if (empty($user)) {
			$this->_redirect('/index/kicked');
			ErrorHelper::error('Du är utloggad!');
			return false;
		} else {
			if (!@$user['admin'] && !@$user['entrance']) {
				$this->_redirect('/index/kicked');
				ErrorHelper::error('Du har inte rättighet att vara här!');
				return false;
			} else {
				return true;
			}
		}
	}
	
	public function index()
	{
		
	}
	
	public function check()
	{
		$member = Model::getModel('member');
		if (!ctype_digit($_REQUEST['SSN'])) { // Om det inte bara är nummer, så är det ett personnummmer
			$member_want = $member->getMemberBySSN($_REQUEST['SSN']);
			
			if (!empty($member_want[0])) {
				$user = Model::getModel('user');
				$user_want =  $user->getByMemberID($member_want[0]['PersonID']);
				
				if (!empty($user_want[0])) {
					$order = Model::getModel('order');
					$order_want = $order->getOrderFromUserAndStatus($user_want[0]['id'], 'COMPLETED');
					
					if (!empty($order_want)) {
						$orders_values = Model::getModel('ordersvalues');
						$orders_values_want = $orders_values->getOrderValuesFromOrder($order_want[0]['id']);
						$this->_set('user_want', $user_want[0]);
						$this->_set('member_want', $member_want[0]);
						$this->_set('order_want', $order_want);
						$this->_set('orders_values_want', $orders_values_want);
					}
				}
			}
		} else { // Its an user number
			$order = Model::getModel('order');
			$user = Model::getModel('user');
			$member = Model::getModel('member');
			$orders_values = Model::getModel('ordersvalues');
			// Get the user
			$user_want = array_pop($user->getByMemberID($_REQUEST['SSN']));
			// Get the order
			$order_want = $order->getLastOrderByUserId($user_want['id']);
			// Get the member
			$member_want = $member->getMemberByID($user_want['member_id']);
			// Get the order values
			$orders_values_want = $orders_values->getOrderValuesFromOrder($order_want['id']);
			$this->_set('user_want', $user_want);
			$this->_set('member_want', $member_want);
			$this->_set('order_want', array($order_want));
			$this->_set('orders_values_want', $orders_values_want);
		}
	}
	
	public function checkin()
	{
		$order = Model::getModel('order');
		$order->setStatusById(@$_REQUEST['order_id'], 'COMPLETED');
		$orders_values = Model::getModel('ordersvalues');
		if (!empty($_REQUEST['value'])) {
			foreach ($_REQUEST['value'] as $key => $value) {
				$orders_values->markGiven($key);
			}
		}
		$this->_redirect('index');
	}
}
