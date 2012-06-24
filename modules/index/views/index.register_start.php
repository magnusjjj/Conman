<form action="<?php echo Router::url("register");?>" method="post">
        <fieldset>
                <h1>Första gången du handlar i NC-store?</h1> <br />
				<div class="italic">If you're not from Sweden and don't have a swedish social security number, please fill in your home country in the box "Land/Country" and then fill in your birth date (YYMMDD) in the first of the two "Personnummer"-boxes. If you have any problems/questions, please contact our customer services at kundtjanst@narcon.se</div><br />

                 Såhär skapar du en användare! Om du är svensk medborgare fyller du i ditt personnummer och trycker sedan på "nästa". Om du inte är svensk medborgare och därmed inte har ett svenskt personnummer så behöver du ändra "land/country" till det land där du är medborgare och sedan fylla i födelsedatum och därefter klicka på "nästa".<br/>
                <br/>
                <label for="country">Land/Country</label>
                <input type="text" name="country" value="Sverige"/>
             	 <br /><br />Om du har problem så kontakta kundtjanst@narcon.se så hjälper vi dig.<br /><br />
				Observera att biljetten är personlig och att du därför ska ange personnumret på den som ska ha biljetten. Om du ska köpa till flera personer, måste alla skapa ett eget konto i ConMan med sitt eget personnummer. Efter att du betalat kan du då föra över de extra biljetterna till de andra personernas konton.<br /><br />
             <label for="pnr[0]">Personnummer (ÅÅMMDD-XXXX):</label>
		<input type="text" size="6" maxlength="6" name="pnr[0]" style="font-family: monospace; width: 6em; float: none; margin-right: 0;"/>-<input type="text" style="font-family: monospace; width: 4em; float: none;" size="4" maxlength="4" name="pnr[1]"/><br/>
                <input type="submit" value="Nästa"/>
        </fieldset>
</form>
