  <?php if(!Auth::user()):?>
	  <a class="blue"  href="<?php echo Router::url('/index/index');?>">Har du ett konto? <h1>Logga in här</h1></a>
	  <a class="black" href="<?php echo Router::url('/index/register_start');?>">Har du inget konto? <h1>Skapa ett nu</h1></a>
	  <?php else:?>
		<?php $user = Auth::user(true);?>
		  	<div class="black"><p class="awesome_margin">Välkommen</p><h1 class="awesome_margin"><?php echo $user['username'];?></h1><p class="small_text"><a class="small_link" href="<?php echo Router::url('/ticket/index');?>">(Orderhistorik</a> / <a class="small_link" href="<?php echo Router::url('/index/logout');?>">logga ut)</a></p></div>
	<?php endif;?>
		<?php  
			ErrorHelper::print_errors();
			if(isset($con))
	                	 $con->render();
		?>
