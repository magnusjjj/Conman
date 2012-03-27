<form action="<?php echo Router::url("register");?>" method="post">
        <fieldset>
                <legend>Skapa användare</legend>
                Först behöver vi kolla om du redan är medlem i <?php echo Settings::$Society;?>. För att göra det, skriv in ditt land och personnummer nedan och tryck på 'nästa'.<br/>
                <br/>
                <label for="country">Land</label>
                <input type="text" name="country" value="Sverige"/>
                Är du utländsk? Har du ett personnummer som inte passar i rutan, är det lungt.
                <br/><br/>
                <label for="pnr[0]">Personnummer:</label>
		<input type="text" size="6" maxlength="6" name="pnr[0]" style="font-family: monospace; width: 6em; float: none; margin-right: 0;"/>-<input type="text" style="font-family: monospace; width: 4em; float: none;" size="4" maxlength="4" name="pnr[1]"/><br/>
                <input type="submit" value="Nästa"/>
        </fieldset>
</form>
