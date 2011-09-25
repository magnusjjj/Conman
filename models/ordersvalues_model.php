<?php
	class OrdersvaluesModel extends Model {
		function addOrderValue($values)
		{
			$this->db->query("INSERT INTO orders_values (order_id, order_alternative_id, value) VALUES ('%s','%s','%s')", $values['order_id'], $values['order_alternative_id'], $values['value']);
		}
		function getOrderValuesFromOrder($orderid)
		{
			return $this->db->query("SELECT orders_alternatives.id, orders_alternatives.name, orders_alternatives.cost
			FROM orders_values
			INNER JOIN orders_alternatives ON orders_values.order_alternative_id = orders_alternatives.id
			WHERE order_id = '%s'", $orderid);
		}
	}
?>