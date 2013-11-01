<?php
class PayapisaveModel extends Model {
	/*
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`user_id` INT NOT NULL ,
	`order_id` INT NOT NULL ,
	`extref_id` INT NOT NULL ,
	`time` TIMESTAMP NOT NULL ,
	`extref_other` VARCHAR( 255 ) NOT NULL ,
	`originalresponse` TEXT NOT NULL,
	`status` VARCHAR (255) NOT NULL
	*/

	/*
'Status' => 'FAILURE', 'Redirect' => NULL, 'Save' => $OriginalResponse
	*/
	public function save($in)
	{
		$this->_db->query("INSERT INTO payapi_save (user_id, order_id, extref_id, extref_other, originalresponse, status, `type`)
		VALUES ('%s','%s','%s','%s','%s','%s', '%s')",
		$in['user_id'], $in['order_id'], $in['extref_id'], $in['extref_other'], $in['originalresponse'], $in['status'], $in['type']);
		return $this->_db->insertid();
	}

	public function updateWithOrderId($payapi_id, $order_id){
		$this->_db->query("UPDATE payapi_save SET order_id = '%s' WHERE id = '%s' LIMIT 1", $order_id, $payapi_id);
	}

	public function getByExternalReference($extref, $type = 'INIT')
	{
		return $this->_db->query("SELECT * FROM payapi_save WHERE order_id != 0 AND extref_other = '%s' AND `type` = '%s'", $extref, $type);
	}
}
