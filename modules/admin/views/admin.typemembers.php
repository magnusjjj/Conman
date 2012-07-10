<?php
	$name = null;
	foreach($thelist as $member)
	{
		if($member['alternative_name'] !== $name)
		{
			$name = $member['alternative_name'];
			echo "<h1>$name</h1>";
		}
		if($member['PersonID'])
			echo '<span style="display: inline-block; width: 300px;">'.$member['ammount'].'st '.$member['firstName'] . ' ' . $member['lastName'] . ' </span>(' . $member['socialSecurityNumber'] .  ')<br/>';
	}
?>
