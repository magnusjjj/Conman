<table>
	<tr>
		<th>Förälderns namn</th>
		<th>Namn</th>
		<th>Kostnad</th>
		<th>Köpgräns</th>
		<th>Antal köpta</th>
		<th>Köpt för totalt</th>
	</tr>
<?php
	$orders_total = 0;
	foreach($status_orders as $status){
?>
	<tr>
		<td><?php echo $status['parent_name'];?></td>
		<td><?php echo $status['name'];?></td>
		<td><?php echo $status['cost'];?></td>
		<td><?php echo $status['ammount_check'];?></td>
		<td><?php echo $status['ammount'];?></td>
		<td><?php echo $status['cost_total'];?></td>
	</tr>
<?
	$orders_total += $status['cost_total'];
}
?>
	<tr><td colspan="5">Total</td><td><?php echo $orders_total;?></td></tr>
</table>
<table>
<tr>
	<td>
		Antal medlemmar med ordrar:
	</td>
	<td>
		<?php echo $order1[0]['users'];?>
	</td>
</tr>
<tr>
	<td>
		Antal medlemmar som har ordrar gedda till sig: 
	</td>
	<td>
		<?php echo $order2[0]['users'];?>
	</td>
</tr>
</table>
