<?php
class OrdersalternativesModel extends Model {  
	public function getAlternatives()
	{
		return $this->_db->query("SELECT orders_alternatives.*,
		(SELECT SUM(orders_values.ammount) FROM orders INNER JOIN orders_values ON orders_values.order_id = orders.id WHERE status = 'COMPLETED' AND orders_values.order_alternative_id = orders_alternatives.id) AS ammount_compare
		FROM orders_alternatives
		GROUP BY orders_alternatives.id
		ORDER BY `order`;");
	}
	
	public function getAlternativesWithUserCount($user_id) // Ugh, naming.
	{
		return $this->_db->query("SELECT orders_alternatives.*, COUNT(orders.id) AS ammount_compare,
		(SELECT COUNT(*) FROM orders_values INNER JOIN orders ON orders.id = orders_values.order_id AND orders.status = 'COMPLETED'
		WHERE orders.user_id = '%s' AND orders_values.order_alternative_id = orders_alternatives.id) AS users_count
		FROM orders_alternatives
		LEFT OUTER JOIN orders_values ON orders_values.order_alternative_id = orders_alternatives.id
		LEFT OUTER JOIN orders ON orders.id = orders_values.order_id AND orders.status = 'COMPLETED'
		GROUP BY orders_alternatives.id
		ORDER BY `order`;", $user_id);
	}
	
	
	public function getAlternativesMembers()
	{
		return $this->_db->query("SELECT orders_alternatives.name as alternative_name, members.*
		FROM orders_alternatives
		LEFT OUTER JOIN orders_values ON orders_values.order_alternative_id = orders_alternatives.id
		LEFT OUTER JOIN orders ON orders.id = orders_values.order_id AND orders.status = 'COMPLETED'
		LEFT OUTER JOIN users ON orders.user_id = users.id 
		LEFT OUTER JOIN members ON members.PersonID = users.member_id
		ORDER BY orders_alternatives.id, members.firstName, members.lastName
		");
	} 
}
