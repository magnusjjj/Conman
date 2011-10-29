<!DOCTYPE html>
<html>
	<head>
		<title>Entré</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script type="text/javascript" src="<?php echo Settings::$path;?>js/jquery.js"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo Settings::$path;?>templates/ajax/entrance.css"/>
		<script type="text/javascript">
			$(function(){
				$(".focusme").focus();
				$(".back").click(function(){
					window.location = "<?php echo Router::url('index');?>";
				});
			});
		</script>
	</head>
	<body>
		<?php //var_dump($member_want);?>
		<form action="<?php if(empty($order_want)):?><?php echo Router::url('check');?><?php else:?><?php echo Router::url('checkin');?><?php endif;?>" method="POST">
		<div id="heading">
			<h1>Entré</h1>
		</div>
		<div id="content">
			<div class="centercontent">
			<?php if(empty($order_want)):?>
				<h1>Scanna in en biljett, ordernummer, eller skriv in ett personnummer nedan</h1>
					<input type="text" value="" name="SSN" class="focusme"/>
				(Personnummer är i formatet <strong>ÅÅMMDD-XXXX</strong>)
				<div class="error">
					Kunde inte hitta en order. Försök igen.
				</div>
			</div>
			<?php else:?>
				<h1><?php echo $member_want['firstName'] . ' ' . $member_want['lastName'];?> <?php if($order_want[0]['status'] == 'COMPLETED'):?><p style="color: green;">(BETALT)</p><?php else:?><p style="color: red;">(EJ BETALD)<p><?php endif;?></h1>
				<hr/>
				<?php if($order_want[0]['status'] == 'COMPLETED'):?><?php else:?>
					<p style="color: red;">
						Viktigt! När du trycker 'Nästa' kommer ordern markeras som betald, om den inte redan är så markerad. <br/>Du måste alltså ta betalt för att få lämna ut saker.
					</p>
				<?php endif;?>
				<table>
					<thead style="text-align: left;">
						<tr>
							<td>Uthämtad</td><td>Namn</td><td width="1%" style="text-align: right;">Kostnad</td>
						</tr>
					</thead>
					<tbody style="text-align: left;">
						<?php foreach($orders_values_want as $thing):?>
							<tr>
								<td width="10%"><input type="checkbox" name="value[<?php echo $thing['value_id'];?>]" value="y"<?php echo $thing['given'] ? ' checked="checked" ' : '';?>/></td><td><?php echo $thing['name'];?></td><td><?php echo $thing['cost'];?>kr</td>
							</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			<?php endif;?>
		</div>
		<div id="commands">
			<table class="actions" border="0">
			<tr>
				<?php if(empty($order_want)):?>
				<td>
					<input type="submit" value="Nästa (Enter)"/>
				</td>
				<?php else:?>
				<td>
					<input type="button" class="back" value="Avbryt"/>
				</td>
				<td>
					<input type="submit" value="Nästa"/>
				</td>
				<?php endif;?>
			</tr>
			</table>
		</div>
			<?php if(!empty($order_want)):?><input type="hidden" name="order_id" value="<?php echo $order_want[0]['id'];?>"/><?php endif;?>
		</form>
	</body>
</html>
