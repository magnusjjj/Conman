<?php
class PaysontraceModel extends Model {
	public function log($text)
	{
		$this->_db->query("INSERT INTO payson_trace (text) values('%s');", $text);
	}
}
