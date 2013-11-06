<?php
class CFactory {
	public static function getTicketGen($template)
	{
		if (!class_exists("TicketGen")) {
			include(Settings::getRoot() . '/includes/ticketgen.php');
		}
		
		return new TicketGen($template);
	}

    public static function getTicketHelper()
    {
        if(!class_exists('TicketHelper'))
        {
            include_once("tickethelper.php");
        }
        return new TicketHelper();
    }

    public static function getTicketMover()
    {
        if(!class_exists('TicketMover'))
        {
            include_once("ticketmover.php");
        }
        return new TicketMover();
    }
}
