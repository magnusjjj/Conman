<?php
class OrderscodesModel extends Model {
    public function getCode($code)
    {
	return array_pop($this->_db->query("SELECT * FROM orders_codes WHERE `code` = '%s' LIMIT 1;", $code));
    }
    
    public function markCode($code_id, $user_id){
	$this->_db->query("UPDATE orders_codes SET `used_by` = '%s' WHERE `id` = '%s' LIMIT 1;", $user_id, $code_id);
    }
}