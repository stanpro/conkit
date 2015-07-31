<?
config::set('error-reporting-lo', E_ALL & ~E_STRICT);
config::set('error-reporting-hi', E_ALL & ~E_NOTICE & ~E_STRICT);
config::set('template-prefix','');
config::set('template-suffix','.tpl.php');
config::set('model-prefix','');
config::set('model-suffix','.mod.php');
config::set('cms-realm','Phella 2 CMS Kit');
config::set('cms-css','?core-module=cms-resource&file=cms.css');
config::set('cms-js','?core-module=cms-resource&file=cms.js');
config::set('jquery','https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js');

class config
{
	static $items= array();
	//=============================================================================
	function set($key,$value)
	{
		config::$items[$key]= $value;
	}
	//=============================================================================
	function get($key)
	{
		if (isset(config::$items[$key])) return config::$items[$key];
		return null;
	}
}