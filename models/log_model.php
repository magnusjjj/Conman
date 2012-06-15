<?php
class LogModel extends Model {

	
	public function log($event, $content, $vars = null)
	{
		$vars = ($vars !== null) ? " (" . var_export($vars, true) . ")" : "";
		$this->_db->query("INSERT INTO log (user_id, event, content) values('%s', '%s', '%s');", Auth::user(), $event, $content . $vars);
	}
}


?>