<?php if($boughtticket) {
	echo '<p class="nomargin">Tack för att du har köpt en biljett till ' . Settings::$EventName . '! &lt;3
	<a href="' . Router::url('getticket') . '">Här hittar du den i pdf-format.</a></p>
	<p class="nomargin">Skriv ut och ta med dig biljetten till konventet för att få ditt inträde och allt annat du eventuellt har beställt. Biljetten kan skrivas ut hur många gånger som helst, så oroa dig inte för att tappa bort den, om det händer skriver du bara ut en ny! Streckkoden på biljetten är kopplad till dig som användare och ger oss information om alla dina köp, oavsett vad som står på biljetten!</p>
	<p class="nomargin">Orderlistan nedan gäller inte som biljett. Har du några frågor om biljetter eller annat rörande ditt köp kan du kontakta ' . Settings::$CustomerserviceEmail . '</p>';
} else {
	echo '<div class="nobox">Tack för att du har köpt ' . Settings::$EventName . '-produkter! &lt;3<br></br>
	<a href="' . Router::url('getticket') . '">Här hittar du ditt orderkvitto i pdf-format.</a></div>
	<div class="nobox">Skriv ut kvittot och ta med dig det till konventet för att få dina produkter. Observera att du ännu inte har köpt någon inträdesbiljett till ' . Settings::$EventName . ' utan bara produkter än så länge. Du kan skriva ut kvittot hur många gånger som helst och varje gång du uppdaterar din beställning med fler produkter, kom då tillbaka hit för att hämta hem och skriva ut den senaste versionen.</div>
	<div class="nobox">Nedan har du din orderlista i textformat. Har du några frågor om biljetter eller annat rörande ditt köp kan du kontakta ' . Settings::$CustomerserviceEmail . '</div>';
}?>

<br />
<?php
	foreach($orders as $key => $order):
?>

<table class="receipt">
	<tr>
		<th class="headeralign">Order #<?php echo $order['id'];
		
		if($order['status'] != 'COMPLETED') {
			echo " - Ej betald";
		} else {
			echo " - Betald";
		}
		?></th>
	</tr>
	<?php $summa = 0;
	foreach($ordersvalues[$key] as $i => $order_item):?>
		<?php if ($order_item['name'] != '') { //Jättefulfix :P
			$partsum = $order_item['ammount']*$order_item['cost'];
			echo '<tr>
				<td class="name">' . $order_item['name'] . ' ( ' . $order_item['cost'] . ' kr/st)</td>
				<td class="amount">' . $order_item['ammount'] . ' st</td>				
				<td class="price">' . $partsum . ' kr</td>
			</tr>';
			$summa += $order_item['cost']*$order_item['ammount'];}
 		endforeach;
		echo '<tr>
		<td class="name">Summa:</td>
		<td class="amount"></td>			
		<td class="sum">' . $summa . ' kr</td>
		</tr>'?>
</table>
<hr>
<br />
<?php 	endforeach;?>



<p class="nomargin"><a href="<?php echo Router::url('buystuff');?>">Gör ytterligare beställningar</a></p>
<p class="nomargin"><a href="<?php echo Router::url('move');?>">Överför produkt till annan användare</a></p>
<p class="nomargin">Du behöver ett program som klarar av att läsa pdf-filer för att öppna biljetten, <a href="http://get.adobe.com/se/reader/">ladda hem ett här.</a></p>
