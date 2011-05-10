<?php
	class TicketController extends Controller {
		function index()
		{
			if(!Auth::user())
				die("Du är utloggad. Ledsen.");
		}
	}
?>