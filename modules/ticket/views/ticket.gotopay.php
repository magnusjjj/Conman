<?php if(!(isset(Settings::$AllowPayson) && !Settings::$AllowPayson)):?>
	<?php if(isset($link)):?>
		<a href="<?php echo $link;?>">Gå vidare!</a>
	<?php endif;?>
<?php else:?>
	Du har nu lagt din order. Gå till kassan och ange ordernummer <strong><?php echo $order_id;?></strong>
	<h2>Kom bara ihåg att (<a href="<?php echo Router::url('/index/logout');?>">Logga ut</a>)</h2>
<?php endif;?>
