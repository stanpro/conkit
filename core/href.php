<?
class href
{
	var $module;
	var $args= array();
	var $anchor;
	//=============================================================================
	function __construct()
	{
		$args= func_get_args();
		while (is_array($args[0])) $args= $args[0];
//debug::dump($args);
		reset($args);
		if (key($args)===0)
		{
			if (count($args)%2) 
			{
				$this->module= $args[0];
				unset($args[0]);
			}
			else $this->module= core::moduleName();
			
			while(current($args)!==false)
			{
				$name= current($args);
				$val= next($args);
				if ($name=='#') $this->anchor= $val;
				elseif ($val==null) unset($this->args[$name]);
				else $this->args[$name]= $val;
				next($args);
			}
		}
		else $this->args= $args;
	}

	//=============================================================================
	function toString()
	{

		if ($this->module==core::$config['default-module'] && !$this->args) 
		{
			$url= (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'],0,-9); // cut off "index.php";
		}
		else $url= '?'.http_build_query(array_merge(array(core::$config['module-var']=>$this->module),$this->args));
		if ($this->anchor) $url.= '#'.$this->anchor;
		return $url;
	}

	//=============================================================================
	static function url()
	{
		$obj= new href(func_get_args());
		return $obj->toString();
	}

	//=============================================================================
	static function urlAdd()
	{
		$obj= new href(func_get_args());
		$obj->args= array_merge(core::$reqUrl,$obj->args);
		return $obj->toString();
	}
}
