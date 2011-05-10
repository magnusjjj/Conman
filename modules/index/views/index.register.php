<?php if($status == 'emailsent'):?>
Ett mail har skickats till din registrerade mail, <?php echo $email;?>.
Klicka på länken i mailet för att fortsätta :).

Är det inte din mail? Kontakta <a href="mailto:magnusjjj@gmail.com">magnusjjj@gmail.com - Magnus Johnsson</a>
<?php elseif($status == 'wrong_ssid'):?>
Tyvärr är personnummret du skrev in inte giltligt. <a href="<?php echo Router::url('index');?>">Försök igen</a>
<?php elseif($status == 'not_member'):?>
Vi hittade dig inte i databasen. Är detta fel? Kontakta <a href="mailto:magnusjjj@gmail.com">magnusjjj@gmail.com - Magnus Johnsson</a><br/>
<br/>
Om detta, inte är fel, så får du (måste) du göra något så peppigt som att lbli medlem i Hikari-Kai :).<br/>
Fyll i uppgifterna nedan, klicka på nästa. När du betalar din medlemsavgift blir du medlem :).<br/>
Du måste fylla i alla uppgifter markerade med *<br/>
<?php
	if(@$not_accepted || @$not_filled)
		echo "<ul>";
	if(@$not_accepted)
		echo "<li>Du fyllde i allt rätt, men du glömde godkänna stadgarna</li>";
	if(@$not_filled)
		echo "<li>Du har tyvärr inte fyllt i alla fält du behövde (de är markerade med *). Försök igen.</li>";
	if(@$not_accepted || @$not_filled)
		echo "</ul>";
?>
<form action="<?php echo Router::url('register')?>" method="post">
	Juridiskt kön*: <input type="radio" value="K" name="memberdata[gender]"<?php echo @$_REQUEST['memberdata']['gender'] == 'K' ? 'checked="checked"' : '';?>/>Kvinna<input type="radio" value="M" name="memberdata[gender]" <?php echo @$_REQUEST['memberdata']['gender'] == 'M' ? 'checked="checked"' : '';?>/>Man<br/>
	Förnamn*: <input type="text" name="memberdata[firstName]" value="<?php echo @$_REQUEST['memberdata']['firstName'];?>"/><br/>
	Efternamn*: <input type="text" name="memberdata[lastName]" value="<?php echo @$_REQUEST['memberdata']['lastName'];?>"/><br/>
	CO-adress: <input type="text" name="memberdata[coAddress]" value="<?php echo @$_REQUEST['memberdata']['coAddress'];?>"/><br/>
	Adress*: <input type="text" name="memberdata[streetAddress]" value="<?php echo @$_REQUEST['memberdata']['streetAddress'];?>"/><br/>
	Postnummer*: <input type="text" name="memberdata[zipCode]" value="<?php echo @$_REQUEST['memberdata']['zipCode'];?>"/><br/>
	Postort*: <input type="text" name="memberdata[city]" value="<?php echo @$_REQUEST['memberdata']['city'];?>"/><br/>
	Land*: <input type="text" name="memberdata[country]" value="<?php echo @$_REQUEST['memberdata']['country'];?>"/><br/>
	Telefonnummer*: <input type="text" name="memberdata[phoneNr]" value="<?php echo @$_REQUEST['memberdata']['phoneNr'];?>"/><br/>
	Mobilnummer: <input type="text" name="memberdata[altPhoneNr]" value="<?php echo @$_REQUEST['memberdata']['altPhoneNr'];?>"/><br/>
	Email*: <input type="text" name="memberdata[eMail]" value="<?php echo @$_REQUEST['memberdata']['eMail'];?>"/><br/>
	<input type="hidden" name="pnr[0]" value="<?php echo @$_REQUEST['pnr'][0];?>"/>
	<input type="hidden" name="pnr[1]" value="<?php echo @$_REQUEST['pnr'][1];?>"/>
	Stadgar:<br/>
	<textarea rows="20" cols="50">
§1 FÖRENINGENS NAMN
Föreningens namn är Hikari-Kai.

§2 FÖRENINGENS SÄTE
Styrelsen har sitt säte i Göteborg.

§3 FÖRENINGSFORM
Föreningen är en ideell förening.

§4 FÖRENINGENS SYFTE
Hikari-kai existerar i syfte att sprida östasiatisk kultur, i huvudsak den Japanska kulturen, främst i form av arkadspel, Tv-Spel, musikspel, karaoke, anime, manga och film.

§5 OBEROENDE
Föreningen är religiöst och partipolitiskt obunden.

§6 VERKSAMHETSÅR
Verksamhetsåret är 1 januari till 31 december.

§7 MEDLEMMAR
Som medlem antas intresserad som godkänner dessa stadgar och aktivt tar ställning för ett medlemskap genom att årligen betala föreningens medlemsavgift och göra en skriftlig anmälan till föreningen. Avgiftens storlek beslutas på årsmötet. En medlem som allvarligt skadar föreningen kan avstängas av styrelsen. Avstängd medlem måste diskuteras på nästa årsmöte, medlemmen får rösta i sin egen sak. Antingen så upphävs då avstängningen eller så utesluts medlemmen. Styrelsen eller årsmöte kan alltså upphäva avstängning och uteslutning.

§8 STYRELSEN
Styrelsen ansvarar för föreningens medlemslista, bidragsansökningar, medlemsvärvning, beslut som tas på årsmöten och övrig verksamhet. Föreningens styrelse består av ordförande, kassör och sekreterare. Vid behov kan även vice ordförande och extra ledamöter väljas. Samma person får inte ha flera poster i styrelsen. Styrelsen väljs på årsmöte och tillträder direkt efter valet. Valbar är medlem i föreningen.

§9 REVISORER
För granskning av föreningens räkenskaper och förvaltning väljs på årsmöte en eller två revisorer. Valbar är person som inte sitter i styrelsen. Revisor behöver inte vara medlem i föreningen.

§10 VALBEREDNING
För att ta fram förslag på personer till de i stadgarna föreskrivna valen kan årsmötet välja en eller flera valberedare. Valbar är medlem i föreningen.

§11 ORDINARIE ÅRSMÖTE
Ordinarie årsmöte ska hållas senast den 31 mars varje år. Styrelsen beslutar om tid och plats. För att vara behörigt måste föreningens medlemmar meddelas minst två veckor i förväg. Följande ärenden ska alltid behandlas på ordinarie årsmöte:
1. ) mötets öppnande
2. ) mötets behörighet
3. ) val av mötets ordförande
4. ) val av mötets sekreterare
5. ) val av två personer att justera protokollet
6. ) styrelsens verksamhetsberättelse för förra året
7. ) ekonomisk berättelse för förra året
8. ) revisorernas berättelse för förra året
9. ) ansvarsfrihet för förra årets styrelse
10. ) årets verksamhetsplan
11. ) årets budget och fastställande av medlemsavgift
12. ) val av årets styrelse
13. ) val av årets revisor
14. ) val av årets valberedare
15. ) övriga frågor
16. ) mötets avslutande

§12 EXTRA ÅRSMÖTE
Om styrelsen eller revisor vill eller minst hälften av föreningens medlemmar kräver det skall styrelsen kalla till extra årsmöte. Vid giltigt krav på extra årsmöte kan den som krävt det sköta kallelsen. För att vara behörigt måste föreningens medlemmar meddelas minst två veckor i förväg. På extra årsmöte kan bara de ärenden som nämnts i kallelsen behandlas.

§13 FIRMATECKNING
Föreningens firma tecknas av ordförande och kassör var för sig. Om särskilda skäl föreligger kan annan person utses att teckna föreningens firma.

§14 RÖSTRÄTT
Endast fullt betalande närvarande medlem har rösträtt på årsmöte. På styrelsemöten har endast närvarande ur styrelsen rösträtt. Röstning via fullmakt godtas vid beslut av årsmötet

§15 RÖSTETAL
Alla frågor som behandlas på årsmöte eller styrelsemöte avgörs med enkel röstövervikt om inget annat står i stadgarna. Nedlagda röster räknas ej. Varje person med rösträtt har en röst. Vid lika röstetal får ordförandet avgöra.

§16 STADGAÄNDRING
Dessa stadgar kan ändras endast vid årsmöte eller extra årsmöte. I kallelsen måste det stå att stadgeändring kommer att behandlas. För att ändra i stadgarna krävs att minst två tredjedelar av de avgivna rösterna bifaller ändringen. För ändring av stadgan om (föreningens syfte) §4, Stadgeändring §16 och Upplösning §17 krävs att beslutet tas på två på varandra följande ordinarie årsmöten.

§17 UPPLÖSNING
Upplösning av föreningen kan endast ske genom beslut på årsmöte. Beslut om upplösning skall fattas med minst två tredjedelars majoritet. Förslag om upplösning skall finnas upptaget på kallelsen till årsmötet.Vid föreningens upplösning överlämnas eventuella tillgångar till av årsmötet beslutat ändamål.
	</textarea>
	<br/>
<input type="checkbox" name="seen_rules" value="1"/> * Jag godkänner dessa stadgar, och tillåter Hikari-Kai att spara mina uppgifter
<input type="submit" value="Nästa!"/>
</form>
<?php endif;?>