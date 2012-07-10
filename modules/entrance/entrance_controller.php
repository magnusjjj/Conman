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

	private function _getordersvalues($user_id)
	{
		$order = Model::getModel('order');
		$ordersvalues = Model::getModel('ordersvalues');

		$myorders = $order->getOrderFromUserAndStatus($user_id, 'COMPLETED');
		$mashup = array();
		foreach($myorders as $myorder)
		{
			$the_ordersvalues = $ordersvalues->getOrderValuesFromOrder($myorder['id']);
			foreach ($the_ordersvalues as $value)
			{
				$mashup[] = $value;
			}
		}
		return $mashup;
	}
	
	public function index()
	{
		
	}
	
	public function check()
	{
		$member = Model::getModel('member');
		$user = Model::getModel('user');
		$order = Model::getModel('order');
		$orders_values = Model::getModel('ordersvalues');

		// Input is SSN
		$input = $_REQUEST['SSN'];
		if(empty($input)){
			// TODO: Error
			die("DU MÅSTE SKRIVA I NÅGOT I RUTAN; DUH");
		} else {
			if($input[0] == 'P') // Personnummer
			{
				$input = substr($input, 1);
				$member_want = $member->getMemberBySSN($input);
				if(empty($member_want))
				{
					die("HITTADE INTE MEDLEMEN");
				}
				$user_want =  $user->getByMemberID($member_want[0]['PersonID']);
				$this->_set('member_want', $member_want[0]);
				$this->_set('orders_values_want', $this->_getordersvalues($user_want[0]['id']));
				$this->_set('member_warn', true);
			} else {
				if(ctype_digit($input)) // Ordernummer
				{
					error_reporting(E_ALL);
					$this->_set('orders_values_want', $orders_values->getOrderValuesFromOrder($input));
					$order_want = $order->getOrderById($input);
					$user_want = $user->get($order_want['user_id']);
					$member_want = $member->getMemberByID($user_want[0]['member_id']);
					$this->_set('member_want', $member_want);
					$this->_set('order_want', array($order_want));
				} else { // Streckkod
					$arr = explode('-', $input);
					$member_id = $arr[0];
					$checksum = $arr[1];
					$check = strtoupper( substr(hash('SHA512', $member_id . Settings::$BarKey), 0, 4 ));
					if($check != $checksum)
					{
						die("FAKEAD BILJETT! KONTAKA TEKNIK!");
					} else {
						$member_want = $member->getMemberByID($member_id);
						$user_want =  $user->getByMemberID($member_id);
						$this->_set('orders_values_want', $this->_getordersvalues($user_want[0]['id']));
						$this->_set('member_want', $member_want);
					}
				}
			}
		}
		/*
		if (!ctype_digit($_REQUEST['SSN'])) { // Om det inte bara är nummer, så är det ett personnummme eller en streckod
			
			if (!empty($member_want[0])) {
				
				if (!empty($user_want[0])) {
					$order_want = $order->getOrderFromUserAndStatus($user_want[0]['id'], 'COMPLETED');
					
					if (!empty($order_want)) {
						$orders_values = Model::getModel('ordersvalues');
						$orders_values_want = $orders_values->getOrderValuesFromOrder($order_want[0]['id']);
						$this->_set('user_want', $user_want[0]);
						$this->_set('order_want', $order_want);
					}
				}
			}
		} else { // Its an member number
			$order = Model::getModel('order');
			$user = Model::getModel('user');
			$member = Model::getModel('member');
			// Get the user
			// Get the order
			$order_want = $order->getLastOrderByUserId($user_want['id']);
			// Get the member
			$member_want = $member->getMemberByID($user_want['member_id']);
			// Get the order values
			$orders_values_want = $orders_values->getOrderValuesFromOrder($order_want['id']);
			$this->_set('user_want', $user_want);
			$this->_set('member_want', $member_want);
			$this->_set('orders_values_want', $orders_values_want);
		}*/
	}
	
	public function checkin()
	{
		$order = Model::getModel('order');
		if(@$_REQUEST['order_id'])
		{
			$order->setStatusById(@$_REQUEST['order_id'], 'COMPLETED');
		}
		$orders_values = Model::getModel('ordersvalues');
		if (!empty($_REQUEST['value'])) {
			foreach ($_REQUEST['value'] as $key => $value) {
				$orders_values->markGiven($key);
			}
		}
		$this->_redirect('index');
	}
}
