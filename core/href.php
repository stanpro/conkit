<?
class href
{
	//=============================================================================
	function url()
	{
		$args= href::processArgs(func_get_args());

		$hash='';
		if (isset($args['request']['#']))
		{
			$hash= '#'.$args['request']['#'];
			unset($args['request']['#']);
		}
		$args= href::required($args);
		if (core::config('rewrite-encode') && (!core::config('no-cache') || !in_array($args['template'],core::$config['no-cache'])))
		{
			$url= call_user_func(core::$config['rewrite-encode'],$args['module'],$args['request'],$hash);
		}
		else
		{
			if (isset(core::$config['index.php'])) $url= core::$config['index.php'];
	    	else $url= '';
	    	$pairs= array();
	    	if ($args['module']!=core::$config['default-module']) $pairs[]= core::$config['module-var'].'='.$args['module'];
			foreach ($args['request'] as $name=>$val) if(!is_null($val)) $pairs[]= $name.'='.urlencode($val);
			if ($pairs) $url.= '?'.implode('&',$pairs);
			if (!$url && !isset($args['current'])) $url= (isset($_SERVER['HTTPS '])?'https':'http').'://'.$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'],0,-9); // cut off "index.php"
		}
		$url= $url.$hash;
		return $url;
	}

	//=============================================================================
	function urlAdd()
	{
		$current= core::$reqUrl;
		$pairs= self::chain2pairs(func_get_args());
		$pairs= array_merge($current,$pairs);
		return self::url(core::moduleName(),$pairs);
	}
	
	//=============================================================================
	function processArgs($args)
	{
		if (is_array($args[0])) return $args[0]; // already processed
		$args_processed= array('module'=>core::moduleName(),'request'=>array());
		if ($args[0])  $args_processed['module']= $args[0];
		else $args_processed['current']= true;
		if (count($args)>1)
		{
			unset($args[0]);
			$args_processed['request']= href::chain2pairs($args);
		}
		return $args_processed;
	}

	//=============================================================================
	function required($args) // add up required vars
	{
 		if (isset(core::$config['required-vars']) && !core::reg('ignore-required-vars'))
		{
			foreach (core::$config['required-vars'] as $name=>$data)
			{
				if (!isset($args['request'][$name])) $args['request'][$name]= core::$req[$name];
				if ($args['request'][$name]==$data['ommit']) unset($args['request'][$name]);
			}
		}
  		if (core::reg('ignore-required-vars') && core::$config['required-vars'])
		{
    		foreach (core::$config['required-vars'] as $name=>$data)
			{
				if (isset($args['request'][$name])) unset($args['request'][$name]);
			}
		}
		return $args;
	}

	//=============================================================================
	function chain2pairs($args)
	{
		if (is_array(reset($args))) return reset($args);
		$pairs= array();
		while(!(current($args)===false))
		{
			$pairs[current($args)]=next($args);
			next($args);
		}
		return $pairs;
	}

}