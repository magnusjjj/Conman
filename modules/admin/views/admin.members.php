<script type="text/javascript">
	$(function(){
		$(".openeditmember").click(function(){
			$.ajax({type: 'GET',
					url: "<?php echo Router::url('editmember')?>" + "/" + $(this).attr("title"),
					success: function(data){
						$.fancybox(data.substr());
					},
					'dataType' : 'html'
			});
		});
	});
</script>
<h1>Medlemmar</h1>
Antal medlemmar: <strong><?php echo count($members);?></strong><br/><br/>
<table>
	<tr>
		<th>Personnummer</th>
		<th>Juridiskt kön</th>
		<th>Förnamn</th>
		<th>Efternamn</th>
		<th>Co-Adress</th>
		<th>Gatuadress</th>
		<th>Postnummer</th>
		<th>Område</th>
		<th>Land</th>
		<th>Telefonnummer</th>
		<th>Mobilnummer</th>
		<th>Email</th>
		<th>Blev medlem</th>
		<th>Medlemskap slutar</th>
		<th>Medlem sedan</th>
	</tr>
	<?php foreach($members as $member):?>
	<tr class="openeditmember" title="<?php echo $member['PersonID'];?>">
		<td><?php echo htmlspecialchars($member['socialSecurityNumber']);?></td>
		<td><?php echo $member['gender'] == 'M' ? 'Man' : 'Kvinna';?></td>
		<td><?php echo htmlspecialchars($member['firstName']);?></td>
		<td><?php echo htmlspecialchars($member['lastName']);?></td>
		<td><?php echo htmlspecialchars($member['coAddress']);?></td>
		<td><?php echo htmlspecialchars($member['streetAddress']);?></td>
		<td><?php echo htmlspecialchars($member['zipCode']);?></td>
		<td><?php echo htmlspecialchars($member['city']);?></td>
		<td><?php echo htmlspecialchars($member['country']);?></td>
		<td><?php echo htmlspecialchars($member['phoneNr']);?></td>
		<td><?php echo htmlspecialchars($member['altPhoneNr']);?></td>
		<td><?php echo htmlspecialchars($member['eMail']);?></td>
		<td><?php echo htmlspecialchars($member['membershipBegan']);?></td>
		<td><?php echo htmlspecialchars($member['membershipEnds']);?></td>
		<td><?php echo htmlspecialchars($member['memberSince']);?></td>
	</tr>
	<?php endforeach;?>
</table>
