<?php
class UserModel extends Model {
	public function username_exists($name)
	{
		return @count($this->_db->query("SELECT * FROM users WHERE username = %s;", $name)) ? true : false;
	}
	
	public function get($id)
	{
		return $this->_db->query("SELECT * FROM users WHERE id = %s", $id);
	}

	public function getByUsername($name)
	{
		return array_pop($this->_db->query("SELECT users.* FROM users WHERE username = %s;", $name));
	}
	
	public function getByEmail($name)
	{
		return array_pop($this->_db->query("SELECT users.* FROM users INNER JOIN members ON users.member_id = members.PersonID WHERE members.eMail = %s;", $name));
	}

	public function getByUsernameOrEmail($name)
	{
		return array_pop($this->_db->query("SELECT users.* FROM users LEFT OUTER JOIN members ON users.member_id = members.PersonID WHERE users.username = %s OR members.eMail = %s;", $name, $name));
	}
	
	public function getByMemberID($id)
	{
		return $this->_db->query("SELECT * FROM users WHERE member_id = %s", $id);
	}
	
	public function create($data)
	{
		$allowed_chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVXYZ";
		$thestring = "";
		for($i = 0; $i < 20; $i++)
		{
			$thestring .= $allowed_chars[mt_rand(0, strlen($allowed_chars) -1)];
		}
		$this->_db->query("INSERT INTO users(username,password,salt,member_id) VALUES(%s,%s,%s,%s);", $data['username'], hash('SHA512', $data['password'] . $thestring), $thestring, $data['member_id']);
	}
	
	public function editPass($id, $pass)
	{
		$allowed_chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVXYZ";
		$thestring = "";
		for($i = 0; $i < 20; $i++)
		{
			$thestring .= $allowed_chars[mt_rand(0, strlen($allowed_chars) -1)];
		}
		$this->_db->query("UPDATE users SET password=%s, salt=%s WHERE member_id = %s;", hash('SHA512', $pass . $thestring), $thestring, $id);
	}
	
	public function auth($username, $password)
	{
		$user = $this->_db->query("SELECT users.id, users.salt, users.password FROM users LEFT OUTER JOIN members ON users.member_id = members.PersonID WHERE users.username = %s OR members.eMail = %s;", $username, $username);
		if(!count($user))
		{
			return false;
		}
		return (hash('SHA512', $password . $user[0]['salt']) == $user[0]['password']) ? $user[0]['id'] : false;
	}

	public function getgivenstatus()
	{
		return $this->_db->query("SELECT COUNT(DISTINCT ug.id) as users FROM users AS ug
                INNER JOIN orders AS og ON og.user_id = ug.id AND og.status = 'COMPLETED'
                INNER JOIN orders_values AS ovg ON ovg.order_id = og.id");
	}
	
	public function getgivenstatus2()
	{
		return $this->_db->query("SELECT COUNT(DISTINCT ug.id) as users FROM users AS ug
                INNER JOIN orders AS og ON og.user_id = ug.id AND og.status = 'COMPLETED'
                INNER JOIN orders_values AS ovg ON ovg.order_id = og.id AND ovg.given > 0");
	}
}
