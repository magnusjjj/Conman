<h1>Välkommen till ConMan</h1>
<h1><?php echo Settings::$EventName; ?> Edition</h1>

<div>
    <p>Här, och ingen annanstans, köper du biljetterna till <?php echo Settings::$EventName; ?>!</p>
    <p>Förutom detta kan du också köpa alla andra awesome prylar du behöver för att göra din konventsupplevelse bättre.</p>
    <p>Men, det slutar inte där, för du kan också logga in och kolla på din beställning, tilldela andra konventare biljetter och göra nya beställningar</p>
    <p>Har du redan ett konto kan du logga in här nedanför, annars registrerar du ett konto ovan!</p>
</div>
<form method="post" action="<?php echo Router::url("login");?>">
    <label for="form_username">Användarnamn<br />eller epost-adress:</label><input type="text" name="username" id="form_username" />
    <label for="form_password">Lösenord:</label><input type="password" name="password" id="form_password" />
	<br style="clear: both" />
    <div style="text-align:center"><input type="submit" value="Logga in" /></div>
</form>

<p class="nomargincenter">
    Problem med att logga in? :(
</p>
<p class="nomargincenter">
    <a href="<?php echo Router::url("forgetPass");?>">Återställ ditt lösenord</a> eller <a href="<?php echo Router::url('register_start');?>">Skapa ett konto</a>
</p>
