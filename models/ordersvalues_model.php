<?php
class OrdersvaluesModel extends Model {
	public function addOrderValue($values)
	{
		$this->_db->query("INSERT INTO orders_values (order_id, order_alternative_id, value, ammount) VALUES ('%s','%s','%s','%s')", $values['order_id'], $values['order_alternative_id'], $values['value'], $values['ammount']);
	}
	
	public function getOrderValuesFromOrder($orderid)
	{
		return $this->_db->query("SELECT orders_alternatives.id, orders_values.id as value_id, orders_alternatives.name, orders_alternatives.cost, orders_values.given, orders_values.value, orders_values.ammount
		FROM orders_values
		LEFT OUTER JOIN orders_alternatives ON orders_values.order_alternative_id = orders_alternatives.id
		WHERE order_id = '%s'", $orderid);
	}
	
	public function listOrderValues()
	{
		return $this->_db->query("SELECT order_id, orders_alternatives.name, orders_alternatives.cost, value, orders_values.ammount
		FROM orders_values
		INNER JOIN orders_alternatives ON orders_values.order_alternative_id = orders_alternatives.id
		INNER JOIN orders ON orders.id = orders_values.order_id 
		WHERE orders.status = 'COMPLETED'");
	}
	
	public function markGiven($id)
	{
		$this->_db->query("UPDATE orders_values SET given = 1 WHERE id = '%s'", $id);
	}

	public function delete($id)
	{
		$this->_db->query("DELETE FROM orders_values WHERE id = '%s' LIMIT 1", $id);
	}

	public function updateammount($id, $ammount)
	{
		$this->_db->query("UPDATE orders_values SET ammount = '%s' WHERE id = '%s' LIMIT 1", $ammount, $id);
	}

	public function getByUserIDAndAlternativeID($user_id, $alternative_id)
	{
		return $this->_db->query("SELECT orders_values.* FROM orders_values INNER JOIN orders ON orders_values.order_id = orders.id WHERE orders.user_id = '%s' AND orders_values.order_alternative_id = '%s' AND orders.status = 'COMPLETED'", $user_id, $alternative_id);
	}
}
