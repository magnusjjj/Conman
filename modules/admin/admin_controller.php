<?php
class AdminController extends Controller 
{
	public function __construct()
	{
		parent::__construct();
		Settings::$Template = 'admin';
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
			if (!@$user['admin']) {
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
		$order = Model::getModel('order');
		$this->_set('orders', $order->listOrders());
		$ordersvalues = Model::getModel('ordersvalues');
		$the_values = $ordersvalues->listOrderValues();
		$simplevalues = array();
		foreach ($the_values as $value) {
			$simplevalues[$value['order_id']][] = $value;
		}
		$this->_set('ordervalues', $simplevalues);
	}
	
	public function members()
	{
		$member = Model::getModel('member');
		$this->_set('members', $member->getMemberList());
	}
	
	public function editmember($id)
	{
		Settings::$Template = 'ajax';
		$member = Model::getModel('member');
		$this->_set('member', $member->getMemberByID($id));
	}
	
	public function editmemberpost()
	{
		$member = Model::getModel('member');
		$member->update(@$_REQUEST['memberdata']);
		$this->_redirect('members');
	}
	
	public function typemembers()
	{
		$orders_alternatives = Model::getModel('ordersalternatives');
		$this->_set('thelist', $orders_alternatives->getAlternativesMembers());
	}

	public function status()
	{
		$orders_alternatives = Model::getModel('ordersalternatives');
		$users = Model::getModel('user');
		$this->_set('status_orders', $orders_alternatives->getAlternativesStatus());
		$this->_set('order1', $users->getgivenstatus());
		$this->_set('order2', $users->getgivenstatus2());
	}
}
