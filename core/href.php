<?
class href
{
	//=============================================================================
	function url()
	{
		$args= href::processArgs(func_get_args());

		// if system folder has direct HTTP access;
		//? if ($args['template']=='(shared)' && $CONFIG['shared-http']) return $CONFIG['shared-http'].$args['req']['document'];

		$hash='';
		if (isset($args['request']['#']))
		{
			$hash= '#'.$args['request']['#'];
			unset($args['request']['#']);
		}
		$args= href::required($args);
		if (isset(config::$items['rewrite-encode']) && (!isset(config::$items['no-cache']) || !in_array($args['template'],core::$config['no-cache'])))
		{
			$url= call_user_func(config::$items['rewrite-encode'],$args['module'],$args['request'],$hash);
		}
		else
		{
			if (isset(config::$items['index.php'])) $url= config::$items['index.php'];
	    	else $url= '';
	    	$pairs= array();
	    	if ($args['module']!=config::$items['default-module']) $pairs[]= config::$items['module-var'].'='.$args['module'];
			foreach ($args['request'] as $name=>$val) if(!is_null($val)) $pairs[]= $name.'='.urlencode($val);
			if ($pairs) $url.= '?'.implode('&',$pairs);
			if (!$url && !isset($args['current'])) $url= (isset($_SERVER['HTTPS '])?'https':'http').'://'.$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'],0,-9); // cut off "index.php"
		}
		/*?
		if (isset($INPUT['(crawler)']))
		{
			if (isset($args['req']['(crawler)'])) unset($args['req']['(crawler)']);
			$OUTPUT['phella']['(crawler)'][]=$args;
		}
		*/
		$url= $url.$hash;
		//? if ($CONFIG['href-html-spec']) $url= htmlspecialchars($url);
		return $url;
	}

	//=============================================================================
	function urlAdd()
	{
		$current= core::$requestUrl;
		$pairs= self::chain2pairs(func_get_args());
		$pairs= array_merge($current,$pairs);
		return self::url(core::module(),$pairs);
	}
	
	//=============================================================================
	function processArgs($args)
	{
		if (is_array($args[0])) return $args[0]; // already processed
		$args_processed= array('module'=>core::module(),'request'=>array());
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
				if (!isset($args['request'][$name])) $args['request'][$name]= core::$request[$name];
				if ($args['request'][$name]==$data['ommit']) unset($args['request'][$name]);
			}
		}
  		if (core::reg('ignore-required-vars') && config::$items['required-vars'])
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