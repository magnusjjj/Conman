<?php
class LogModel extends Model {

	// easing up the product (alternative) transfer logging by keeping track of the transfer id
	private $transfer_id = null;
	
	public function log($event, $content, $vars = null)
	{
		if ($vars !== null)
		{
			if (is_array($vars))
			{
				$formatted_vars = "";
				foreach($vars as $key => $value)
					$formatted_vars .= "{$key}: " . print_r($value, true) . "\n";
				$vars = $formatted_vars;
			}
			$vars = "\n{$vars}";
		}
		else
		{
			$vars = "";
		}
		$this->_db->query("INSERT INTO log (user_id, event, content) values('%s', '%s', '%s');", Auth::user(), $event, $content . $vars);
	}
	
	public function logTransfer($from_user_id, $to_user_id, $from_order_id, $to_order_id, $alternative_id, $amount)
	{
		if ($this->transfer_id === null)
		{
			$this->_db->query("INSERT INTO `log_transfer`(`from_user_id`, `to_user_id`) VALUES ('{$from_user_id}', '{$to_user_id}')");
			$this->transfer_id = $this->_db->insertid();
		}
		$this->_db->query("INSERT INTO `log_transfer_values`(`log_transfer_id`, `from_order_id`, `to_order_id`, `alternative_id`, `ammount`) VALUES ('{$this->transfer_id}', '{$from_order_id}', '{$to_order_id}', '{$alternative_id}', '{$amount}')");
		return $this->transfer_id;
	}

}

?>