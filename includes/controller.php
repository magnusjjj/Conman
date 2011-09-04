<?php
	class Controller {
		var $view; // The view file name
		var $name;
		
		var $vars = array();
		
		function set($name, $variables)
		{
			$this->vars[$name] = $variables;
		}
		
		function redirect($path)
		{
			header('Location: ' . Router::url($path, true));
		}
		
		function render()
		{
			foreach($this->vars as $key => $var)
			{
				$$key = $var;
			}
			if(file_exists('modules/'.$this->name.'/views/'.$this->view))
				include('modules/'.$this->name.'/views/'.$this->view);
		}
	}
?>