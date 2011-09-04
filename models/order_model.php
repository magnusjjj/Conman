<?php
	class OrderModel extends Model {
		function addOrder($values)
		{
			$this->db->query("INSERT INTO orders (user_id, payson_token) VALUES ('%s','%s')", $values['user_id'], $values['payson_token']);
			return $this->db->insertid();
		}
		
		function getOrderFromUserAndStatus($userid, $status)
		{
			return $this->db->query("SELECT * FROM orders WHERE user_id = '%s' AND status = '%s' LIMIT 1;", $userid, $status);
		}
		
		function setStatus($payson_token, $status)
		{
			$this->db->query("UPDATE orders SET `status` = '%s'  WHERE `payson_token` = '%s';", $status, $payson_token);
		}
	}
?>