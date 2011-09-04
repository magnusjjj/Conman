Hej <?php $user = Auth::user(true); echo $user['username'];?> (<a href="<?php echo Router::url('/index/logout');?>">Logga ut</a>)<br/>
Du har en biljett, <a href="<?php echo Router::url('getticket');?>">klicka här för att skriva ut den.</a><br/>
Du behöver ett pdfläsarprogram, <a href="http://get.adobe.com/se/reader/">ladda hem ett här</a>