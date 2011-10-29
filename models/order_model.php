<?php
	class OrderModel extends Model {
	
		function listOrders()
		{
			return $this->db->query("SELECT orders.*, members.firstName, members.lastName, members.socialSecurityNumber FROM orders
			LEFT OUTER JOIN users ON orders.user_id = users.id
			LEFT OUTER JOIN members ON users.member_id = members.PersonID
			WHERE orders.status = 'COMPLETED'
			ORDER BY members.firstName, members.lastName;");
		}
		
		function addOrder($values)
		{
			$this->db->query("INSERT INTO orders (user_id, payson_token) VALUES ('%s','%s')", $values['user_id'], $values['payson_token']);
			return $this->db->insertid();
		}
		
		function getOrderById($id)
		{
			$ans = $this->db->query("SELECT * FROM orders WHERE id = '%s' LIMIT 1;", $id);
			return @$ans[0];
		}
		
		function getOrderFromUserAndStatus($userid, $status)
		{
			return $this->db->query("SELECT * FROM orders WHERE user_id = '%s' AND status = '%s' LIMIT 1;", $userid, $status);
		}
		
		function setStatus($payson_token, $status)
		{
			$this->db->query("UPDATE orders SET `status` = '%s'  WHERE `payson_token` = '%s';", $status, $payson_token);
		}
		
		function setStatusById($id, $status)
		{
			$this->db->query("UPDATE orders SET `status` = '%s'  WHERE `id` = '%s';", $status, $id);
		}
	}
?>
