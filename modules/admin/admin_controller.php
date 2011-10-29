<?php
	class AdminController extends Controller {
		var $is_true = true;
		var $ticket_disabled = false;
		
		function __construct()
		{
			parent::__construct();
			Settings::$Template = 'admin';
			if(!$this->checklogin()) return;
		}
		
		private function checklogin()
		{
			$user = Auth::user(true);
			if(empty($user))
			{
				$this->redirect('/index/kicked');
				ErrorHelper::error('Du är utloggad!');
				return false;
			} else {
				if(!@$user['admin']){
					$this->redirect('/index/kicked');
					ErrorHelper::error('Du har inte rättighet att vara här!');
					return false;
				} else {
					return true;
				}
			}
		}
		
		function index()
		{
			$order = Model::getModel('order');
			$this->set('orders', $order->listOrders());
			$ordersvalues = Model::getModel('ordersvalues');
			$the_values = $ordersvalues->listOrderValues();
			$simplevalues = array();
			foreach($the_values as $value)
			{
				$simplevalues[$value['order_id']][] = $value;
			}
			$this->set('ordervalues', $simplevalues);
		}
		
		function members()
		{
			$member = Model::getModel('member');
			$this->set('members', $member->getMemberList());
		}
		
		function editmember($id)
		{
			Settings::$Template = 'ajax';
			$member = Model::getModel('member');
			$this->set('member', $member->getMemberByID($id));
		}
		
		function editmemberpost()
		{
			$member = Model::getModel('member');
			$member->update(@$_REQUEST['memberdata']);
			$this->redirect('members');
		}
		
		function typemembers()
		{
			$orders_alternatives = Model::getModel('ordersalternatives');
			$this->set('thelist', $orders_alternatives->getAlternativesMembers());
		}
	}
?>