Hej <?php $user = Auth::user(true); echo $user['username'];?> (<a href="<?php echo Router::url('/index/logout');?>">Logga ut</a>)<br/>
Du har en biljett, <a href="<?php echo Router::url('getticket');?>">klicka här för att skriva ut den.</a><br/>
<br/>
Orderlistan nedan gills INTE som din biljett:<br/>
<table>
<?php
	foreach($orders as $key => $order):
?>
	<tr>
		<td>
			Order <?php echo $order['id'];?>
		</td>
		<td>
			<ul>
			<?php foreach($ordersvalues[$key] as $i => $order_item):?>
			<li><?php echo $order_item['name'];?> - <?php echo $order_item['cost'];?>kr</li>
			<?php endforeach;?>
			<ul>
		</td>
	</tr>
<?php 	endforeach;?>
</table>


<a href="<?php echo Router::url('buystuff');?>">Köp mer!</a>

Du behöver ett pdfläsarprogram, <a href="http://get.adobe.com/se/reader/">ladda hem ett här</a>
