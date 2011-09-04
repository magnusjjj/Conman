<?php
	class TicketOrderItem{
		var $items = array();
		function __construct($id, $value, $lookup)
		{
			$opt = json_decode($lookup[$id]['extra']);
			unset($value['force']);
			if(empty($value))
			{
				throw new Exception("Du måste köpa en biljett!");
			}
			foreach($value as $key => $val)
			{
				$this->items[] = array('name' => $key != 'weekend' ? $key : 'Helhelg', 'cost' => (!empty($opt->$key) ? $opt->$key : $opt->days->$key), 'number' => 1);
			}
		}
		function getItem()
		{
			return array_shift($this->items);
		}
	}
?>