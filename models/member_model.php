<?php
class MemberModel extends Model
{
	public function getMemberBySSN($SSN)
	{
		return $this->_db->query("SELECT * FROM members WHERE socialSecurityNumber = '%s';", $SSN);
	}
	
	public function getMemberList()
	{
		return $this->_db->query("SELECT * FROM members LEFT OUTER JOIN users ON members.PersonID = users.member_id GROUP BY members.PersonID ORDER BY firstname,lastname;");
	}
	
	public function create($v)
	{
		$this->_db->query("INSERT INTO members
		(socialSecurityNumber, gender, firstName, lastName, coAddress, streetAddress, zipCode, city, country, phoneNr, altPhoneNr, eMail, memberFee, memberSince)
		VALUES
		('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',0, NOW())
		", $v['socialSecurityNumber'], $v['gender'], $v['firstName'], $v['lastName'], @$v['coAddress'], $v['streetAddress'], $v['zipCode'], $v['city'], $v['country'], $v['phoneNr'], @$v['altPhoneNr'], $v['eMail']); 
		return $this->_db->insertid();
	}
	
	public function update($v)
	{
		$this->_db->query("UPDATE members SET socialSecurityNumber = '%s', gender = '%s', firstName = '%s', lastName = '%s', coAddress = '%s', streetAddress = '%s', zipCode = '%s', city = '%s', country = '%s', phoneNr = '%s', altPhoneNr = '%s', eMail = '%s' WHERE PersonID = '%s' LIMIT 1",
		$v['socialSecurityNumber'], $v['gender'], $v['firstName'], $v['lastName'], @$v['coAddress'], $v['streetAddress'], $v['zipCode'], $v['city'], $v['country'], $v['phoneNr'], @$v['altPhoneNr'], $v['eMail'], $v['PersonID']); 
	}
	
	public function getMemberByUserID($id)
	{
		$member = $this->_db->query("SELECT * FROM members WHERE PersonID = (SELECT `member_id` FROM users WHERE `id` = '%s');", $id);
		return !empty($member[0]) ? $member[0] : false;
	}
	
	public function getMemberByID($id)
	{
		$member = $this->_db->query("SELECT * FROM members WHERE PersonID = '%s';", $id);
		return !empty($member[0]) ? $member[0] : false;
	}
	
	public function updateMemberShip($id)
	{
		$this->_db->query("UPDATE members SET membershipBegan = NOW(), membershipEnds = NOW() WHERE PersonID = '%s' LIMIT 1", $id);
	}
}
