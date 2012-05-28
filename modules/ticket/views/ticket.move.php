<?php //var_dump($ordersvalues);?>
<?php //var_dump($tree_simple);?>
<form style="overflow: visible" action="<?php echo Router::url('move');?>" method="POST">
	<p class="nomargin">Om du vill ge bort eller sälja vidare en NärConbiljett eller något annat du köpt av oss är det tre saker som är viktiga att tänka på:</p>
	<ol class="move">
		<li>Det är absolut förbjudet att sälja vidare till ett högre pris än det varan har köpts för. Om vi upptäcker att någon har försökt sälja till högre priser kommer vi att beslagta de produkterna och erbjuda den som rapporterade förseelsen att köpa produkten till vårt utsatta pris.</li><br />
		<li>För att överföringen ska vara giltlig behöver den göras här i ConMan, det räcker alltså inte bara med att ge pappersbiljetten till personen du vill göra överföringen till.</li><br />
		<li>Mottagaren av överföringen måste vara registrerad i ConMan för att kunna ta emot den.</li>
   </ol><br />
<p class="nomargin">NärCon rekommenderar vår samarbetspartner <a href="http://www.payson" target="_blank">Payson</a> eller internetbanköverföring för att genomföra betalningen av vidareförsäljningen. Om du och köparen kan komma överens om ett annat sätt att sköta transaktionen på går det naturligtvis också bra. Ni är själva ansvariga för att transaktionen genomförs.</p><br />
<table class="receipt">
		<th class="headeralign">Antal att skicka</th><th class="headeralign">Typ av produkt</th>
	<?php foreach($ordersvalues as $value):?>
		<tr>
			<td>
				<select name="ammount[<?php echo $value['id']?>]">
					<?php for($i = 0; $i <= $value['ammount']; $i++):?>	
					<option <?php echo @$_REQUEST['ammount'][$value['id']] == $i ? ' selected="selected" ' : '' ;?> ><?php echo $i;?></option>
					<?php endfor;?>
				</select>
			</td>
			<td>
				<?php 
					echo $tree_simple[ $tree_simple[ $value['id'] ]['parent'] ]['template_override'] == 'select'  ? $tree_simple[ $tree_simple[ $value['id'] ]['parent'] ]['name'] . ' -  ' . $value['name']: $value['name'];
				?>
			</td>

<!--			<td class="name">
				<?php
					echo $tree_simple[ $tree_simple[ $value['id'] ]['parent'] ]['template_override'] == 'select'  ? $tree_simple[ $tree_simple[ $value['id'] ]['parent'] ]['description'] : $tree_simple[$value['id']]['description'];
				?>
			</td>
-->
		</tr>
	<?php endforeach;?>
	</table><br />
	<p class="nomargin">Mottagarens användarnamn: <input type="text" value="<?php echo @$_REQUEST['usertomoveto'];?>" name="usertomoveto"/></p>
	<p class="nomargin">Ditt lösenord: <input type="password" name="mypassword"></p>
	<p><input type="submit" value="Flytta sakerna"/></p>
</form>
