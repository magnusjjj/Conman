<?php
abstract class Controller 
{
	public $view; // The view file name
	public $name;
	
	private $_vars = array();
	
	public function __construct() // Måste existera
	{
	
	}
	
	protected function _set($name, $variables)
	{
		$this->_vars[$name] = $variables;
	}
	
	protected function _redirect($path)
	{
		header('Location: ' . Router::url($path, true));
	}
	
	public function render()
	{
		foreach ($this->_vars as $key => $var) {
			$$key = $var;
		}
		if (file_exists('modules/'.$this->name.'/views/'.$this->view))
			include('modules/'.$this->name.'/views/'.$this->view);
	}
}
