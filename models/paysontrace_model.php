<?php
	class PaysontraceModel extends Model {
		function log($text)
		{
			$this->db->query("INSERT INTO payson_trace (text) values('%s');", $text);
		}
	}
?>