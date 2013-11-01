<?php
class OrderModel extends Model {

	public function listOrders()
	{
		return $this->_db->query("SELECT orders.*, members.firstName, members.lastName, members.socialSecurityNumber FROM orders
		LEFT OUTER JOIN users ON orders.user_id = users.id
		LEFT OUTER JOIN members ON users.member_id = members.PersonID
		WHERE orders.status = 'COMPLETED'
		ORDER BY members.firstName, members.lastName;");
	}
	
	public function addOrder($values)
	{
		$this->_db->query("INSERT INTO orders (user_id, code_id) VALUES ('%s','%s')", $values['user_id'], $values['code_id']);
		return $this->_db->insertid();
	}
	
	public function getOrderById($id)
	{
		$ans = $this->_db->query("SELECT * FROM orders WHERE id = '%s' LIMIT 1;", $id);
		return @$ans[0];
	}

	public function getLastOrderByUserId($id)
	{
		$ans = $this->_db->query("SELECT * FROM orders WHERE user_id = '%s' ORDER BY `timestamp` DESC LIMIT 1", $id);
		return $ans[0];
	}

	public function getOrdersByUserId($userid)
	{
		return $this->_db->query("SELECT * FROM orders WHERE user_id = '%s' ORDER BY `timestamp` DESC", $userid);
	}

	public function getOrderFromUserAndStatus($userid, $status)
	{
		return $this->_db->query("SELECT * FROM orders
		    WHERE user_id = '%s' AND status = '%s' ORDER BY `timestamp` DESC;",
            $userid, $status);
	}

	public function setStatus($payson_token, $status)
	{
		$this->_db->query("UPDATE orders SET `status` = '%s'  WHERE `payson_token` = '%s';", $status, $payson_token);
	}

	public function setStatusById($id, $status)
	{
		$this->_db->query("UPDATE orders SET `status` = '%s'  WHERE `id` = '%s';", $status, $id);
	}
}
