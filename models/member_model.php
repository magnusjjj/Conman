<?php
	class MemberModel extends Model
	{
		function getMemberBySSN($SSN)
		{
			return $this->db->query("SELECT * FROM members WHERE socialSecurityNumber = '%s';", $SSN);
		}
		
		function create($v)
		{
			$this->db->query("INSERT INTO members
			(socialSecurityNumber, gender, firstName, lastName, coAddress, streetAddress, zipCode, city, country, phoneNr, altPhoneNr, eMail, memberFee, memberSince)
			VALUES
			('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',0, NOW())
			", $v['socialSecurityNumber'], $v['gender'], $v['firstName'], $v['lastName'], @$v['coAddress'], $v['streetAddress'], $v['zipCode'], $v['city'], $v['country'], $v['phoneNr'], @$v['altPhoneNr'], $v['eMail']); 
		}
		
		function getMemberByUserID($id)
		{
			$member = $this->db->query("SELECT * FROM members WHERE PersonID = (SELECT `member_id` FROM users WHERE `id` = '%s');", $id);
			return !empty($member[0]) ? $member[0] : false;
		}
	}
?>