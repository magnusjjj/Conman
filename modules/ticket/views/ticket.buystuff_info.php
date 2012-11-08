<h1>Din <?php echo Settings::$EventName; ?>-biljett är personlig.</h1>
<div>
	<p>Med ett köp av en <?php echo Settings::$EventName; ?>-biljett går du med i föreningen <?php echo Settings::$Society; ?>, vilket till exempel innebär att du täcks av föreningens försäkring om en olycka skulle vara framme. För att försäkringen ska gälla krävs att biljetten är personligt knuten med ditt (besökarens) personnummer.</p>
	<p>Om du är förälder och köper en biljett till din dotter eller son, måste ditt barn alltså ha ett eget konto på Conman.</p>
	<p>Likaså om du köper till en kompis, så måste din kompis skapa ett eget konto på Conman.</p>
	<p>Om du redan har köpt en biljett i ditt eget namn, så går det bra att flytta biljetter i efterhand.</p>
	<p>För att flytta en biljett (och andra artiklar), loggar du in i Conman och klickar på länken "Överför produkt till annan användare".</p>
	<p>Sedan anger du ditt barns eller din kompis användarnamn (eller den epost-adress som användes vid registreringen), väljer vilka artiklar du vill överföra och klickar på "Flytta sakerna".</p>
	<p>&nbsp;</p>
	<p>Har du några frågor så tveka inte att kontakta kundtjänst (du hittar oss på <a href=<?php echo Settings::$CustomerserviceUrl; ?>"><?php echo Settings::$CustomerserviceUrl; ?></a>)!</p>
	<p>&nbsp;</p>
	<p><a href="<?php echo Router::url('/ticket/buystuff'); ?>">Återgå till köpsidan</a></p>
</div>