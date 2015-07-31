<?
include(__DIR__.'/config.php');

//=======================================================
function __autoload($name) 
{
	if (in_array($name,array('href','debug','sql','cms','cmsGui','cmsContext'))) include(__DIR__.'/'.$name.'.php');
	else core::library(core::config('class-sprefix').$name.core::config('class-suffix'));
}

//=======================================================
class core
{
	static $config= array();
	static $request= array();
	static $requestUrl= array();
	static $registry= array();
	static $callStack= array();
	static $prepend= array();
	
	//====================================================
	function start()
	{
		error_reporting(core::$config['error-reporting-lo']);

		foreach ($_COOKIE as $var=>$val) core::$request[$var]= $val;
		if (isset(core::$config['cookie-vars'])) foreach (core::$config['cookie-vars'] as $var=>$config)
		{
			core::$request[$var]= core::filter(core::req($var),$config);
		}

		foreach ($_GET as $var=>$val) core::$request[$var]= core::$requestUrl[$var]= $val;
		unset(core::$requestUrl[core::config('module-var')]);
		foreach ($_POST as $var=>$val) core::$request[$var]= $val;
		foreach ($_FILES as $var=>$val)
		{
			if (is_string($val['name'])) core::$request[$var]= $val;  // <input type=file name=xxx ...>
			else  // <input type=file name=xxx[yyy] ...>
			{
				foreach ($val['name'] as $key=>$void)
				{
		 			core::$request[$var][$key]= array(
						'name'=>$val['name'][$key],
						'type'=>$val['type'][$key],
						'tmp_name'=>$val['tmp_name'][$key],
						'error'=>$val['error'][$key],
						'size'=>$val['size'][$key]
					);
				}
			}
		}

		if (isset(core::$config['cookie-vars'])) foreach (core::$config['cookie-vars'] as $name=>$config) 
		{
			if (isset(core::$request[$name]))
			{
				core::$request[$name]= core::filter(core::$request[$name], $config);
				if (!isset($_COOKIE[$name]) || $_COOKIE[$name]!=core::$request[$name])
				{
					setcookie($name, core::$request[$name], $config['expire']);
				}
				unset(core::$requestUrl[$name]);
			}
		}
		
		// apply required vars
		if (isset(core::$config['required'])) foreach (core::$config['required'] as $name=>$config)
		{
			core::$request[$name]=  core::filter(core::$request[$name],$comfig);
			core::$requestUrl[$name]= core::$request[$name];
		}
		
		if (!core::moduleName())
		{
			if (is_array(core::$config['default-module']))
			{
				$args= href::processArgs(core::$config['default-module']);
				core::$request[core::$config['module-var']]= $args['template'];
				array_merge(core::$reques,$args['req']);
			}
		}
		core::$request[core::$config['module-var']]= str_replace('..','(dot)(dot)',core::moduleName()); //secure upper directories
		core::$request[core::$config['module-var']]= str_replace("\0",'(0)',core::moduleName()); //secure
		core::$request[core::$config['module-var']]= str_replace('<','(lt)',core::moduleName()); //secure
		core::$request[core::$config['module-var']]= str_replace('>','(gt)',core::moduleName()); //secure

		// set session var
		if (core::req(session_name()) || isset(core::$config['session-vars']))
		{
			if (!session_id()) session_start();
			foreach ($_SESSION as $name=>$val) core::$request[$name]= $val;
			if (isset(core::$config['session-vars'])) foreach (core::$config['session-vars'] as $name=>$config)
			{
				core::$request[$name]= core::filter(core::req($name),$config);
				unset(core::$requestUrl[$name]);
			}
		}

		if (core::req('cms-oper')) cms::perform();
		if (core::req('core-module')=='cms-css') cmsGui::generateCss(core::req('target'));
		if (core::req('core-module')=='cms-resource') cmsGui::forward(core::req('file'));

		ob_start();
		if (core::$config['pre-models']) foreach(core::$config['pre-models'] as $model) core::insertModel($model);
		core::insert(core::moduleName());
		$buffer= ob_get_contents();
		ob_end_clean();

		// Process pre- and post-templates
		if (!core::reg('run-naked'))
		{
			ob_start();
			if (core::$config['pre-module']) core::insert(core::$config['pre-module']);
			echo $buffer;
			if (core::$config['post-module']) core::insert(core::$config['post-module']);
			if (core::config('post-models')) foreach(core::$config['post-models'] as $model) core::insertModel($model);
			$buffer= ob_get_contents();
			ob_end_clean();
		}
		
		// Output
		echo $buffer;
		if (core::$prepend) core::error('one or more prepends were not utilized: '.implode(',',array_keys(core::$prepend)));
	}	

	//=============================================================================
	function config($key,$value=null)
	{
		$stored= (isset(self::$config[$key]) ? self::$config[$key] : null);
		if ($value) self::$config[$key]= $value;
		return $stored;
	}
	
	//=============================================================================
	function req($key,$val=null)
	{
		if ($val!==null) core::$request[$key]= $val;
		if (isset(core::$request[$key])) return core::$request[$key];
		else return null;
	}
	
	//=============================================================================
	function reqSession($name,$value)
	{
		if (!session_id()) session_start();
		$_SESSION[$name]= $value;
		core::$request[$name]= $value;
		return $value;
	}
	
	//=============================================================================
	function reqCookie($name,$value,$expire=null)
	{
		if ($expire)
		{
			if (preg_match('/^\d{4}-d{2}-d{2}/',$expire)) $expire= strtotime($expire);
			elseif ($expire<1000000000) $expire+= time();
		}
		setcookie($name, $value, $expire);
		core::$request[$name]= $value;
		return $value;
	}
	
	//=============================================================================
	function reg($key,$val=null)
	{
		if ($val!==null) core::$registry[$key]= $val;
		if (isset(core::$registry[$key])) return core::$registry[$key];
		else return null;
	}
	
	//=============================================================================
	function module()
	{
		if (isset(core::$request[core::$config['module-var']])) return core::$request[core::$config['module-var']];
		return core::$config['default-module'];
	}
	
	//=============================================================================
	function filter($value,$rule)
	{
		if (!isset($rule['default'])) $rule['default']=null;
		if (isset($rule['range'])) $value= core::filterRange($value,$rule['range'],$rule['default']);
		elseif (isset($rule['valid'])) 
		{
			if (strpos($rule['valid'],'w')!==false) $value= preg_replace('/[^a-zA-Z0-9_-]/','',$value);
			if (strpos($rule['valid'],'i')!==false) $value= (int)$value;
			if (strpos($rule['valid'],'h')!==false) $value= htmlspecialchars($value);
			if (strpos($rule['valid'],'u')!==false) $value= strtoupper($value);
			if (strpos($rule['valid'],'l')!==false) $value= strtoupper($value);
			if (strpos($rule['valid'],'t')!==false) $value= trim($value);
			if (strpos($rule['valid'],'@')!==false) 
			{
				$value= trim($value);
				$value= preg_replace('/[^a-zA-Z0-9@_.\-]/','',$value);
				$value= strtolower($value);
			}
		}
		elseif (isset($rule['handler']))
		{
			if (str_word_count($rule['handler'])==1) // function given
			{
				if (function_exists($rule['handler'])) $value= call_user_func($rule['handler'],$value);
				else core::error("Handler function not exists: '{$rule['handler']}'");
			}
		}
		elseif (isset($rule['default'])) $value= $rule['default'];
		if (isset($rule['size'])) $value= substr($value,0,$rule['size']);
		return $value;
	}

	//=============================================================================
	function filterRange($value,$range,$default)
	{
		if (is_array($range))
		{
			if (in_array($value,$range)) return $value;
			if ($default) return $default;
			return reset($range);
		}
		$list= explode(',',$range);
		foreach ($list as $sub)
		{
			$sub= explode('-',$sub,2);
			if (count($sub)==1) $sub[1]=$sub[0];
			if ($sub[0].$sub[1]=='' && $value=='-') return $value;
			if ($sub[0]=='' && $value<=$sub[1]) return $value;
			if ($sub[1]=='' && $sub[0]<=$value) return $value;
			if ($sub[0]<=$value && $value<=$sub[1]) return $value;
		}
		return $default;
	}

	//=============================================================================
	function redirect()
	{
		$args= href::processArgs(func_get_args());
		header('Location: '.core::url($args));
		exit;
	}

	//=============================================================================
	function insert($name)
	{
		$name= core::callStackPush(func_get_args(),'module');

		$file= core::config('model-prefix').$name.core::config('model-suffix');
		if (file_exists($file))
		{
			core::errorReportToggle('hi');
			include($file);
			core::errorReportToggle();
		}
		$file= core::config('template-prefix').$name.core::config('template-suffix');
		if ($result=file_exists($file))
		{
			core::errorReportToggle('hi');
			include($file);
			core::errorReportToggle();
		}
		else core::halt(404,"Template not found '$file'");
 		core::callStackPop();
		return $result;
	}

	//=============================================================================
	function model()
	{
		$name= core::callStackPush(func_get_args(),'model');
  		$file= core::config('model-prefix').$name.core::config('model-suffix');
		if (file_exists($file))
		{
			core::errorReportToggle('hi');
			$res= include($file);
			core::errorReportToggle();
		}
		else core::error("Model not found '$file'");
		core::callStackPop();
		return $res;
	}

	//=============================================================================
	function library($model)
	{
		static $used= array();
		if (isset($used[$model])) return;
		$used[$model]= true;
		return core::model($model);
	}

	//=============================================================================
	function errorReportToggle($new=null)
	{
		static $stack= array();
		if ($new)
		{
			error_reporting(core::$config['error-reporting-'.$new]);
			array_push($stack,core::$config['error-reporting-'.$new]); 
		}
		else
		{
			$new= array_pop($stack);  
			error_reporting($new);
		}
	}

	//=============================================================================
	function args($n=0)
	{
		if ($n<0) return '';
		end(core::$callStack);
		$ptr= key(core::$callStack);
		if ($n==0) return core::$callStack[$ptr]['args'];
		elseif (!isset(core::$callStack[$ptr]['args']) || $n > count(core::$callStack[$ptr]['args'])) return '';
		else return core::$callStack[$ptr]['args'][$n-1];
	}

	//=============================================================================
	function callStackPush($args,$type)
	{
		$modulename= reset($args);
		core::$callStack[]=array('name'=>$modulename,'args'=>array());
		end(core::$callStack);
		$ptr= key(core::$callStack);
		unset($args[key($args)]);
		foreach ($args as $val) core::$callStack[$ptr]['args'][]=$val;
		core::$callStack[]=array('type'=>$type,'name'=>$modulename);
		return $modulename;
	}

	//=============================================================================
	function callStackPop()
	{
		end(core::$callStack);
		unset(core::$callStack[key(core::$callStack)]);
		end(core::$callStack);
		$ptr= key(core::$callStack);
		core::$callStack[$ptr]['status']= 'close';
	}

	//=============================================================================
	function prepend($target,$type='(output)',$value=null)
	{
		if ($type=='(output)')
		{
			$html= "\n";
			if (isset(core::$prepend[$target])) foreach (core::$prepend[$target] as $value) $html.= $value."\n";
			unset(core::$prepend[$target]);
			return $html;
		}
		elseif ($type=='title') $value= '<title>'.$value.'</title>';
		elseif ($type==='description') $value= '<meta name="description" content="'.$value.'" />';
		elseif ($type==='keywords') $value= '<meta name="keywords" content="'.$value.'" />';
		elseif ($type==='charset') $value= '<meta http-equiv=Content-Type content="text/html; charset='.$value.'" />';
		elseif ($type==='favicon') $value= '<link rel="shortcut icon" href="'.$value.'" />';
		elseif ($type=='jquery') $value= '<script type="text/javascript" src="'.core::config('jquery').'"></script>';
		elseif ($type==='cms')
		{
			core::prepend('head','jquery');
			core::prepend('head','css',core::config('cms-css'));
			core::prepend('head','js',core::config('cms-js'));
			core::prepend('head','css','https://fonts.googleapis.com/icon?family=Material+Icons');
			return;
		}
		elseif ($type==='js')
		{
			$type= $value;
			$value= '<script type="text/javascript" src="'.$value.'"></script>';
		}
		elseif ($type==='css') 
		{
			$type= $value;
			$value= '<link rel="stylesheet" type="text/css" href="'.$value.'" />';
		}
		elseif ($type=='script')
		{
			$type= null;
			$value= '<script>'.$value.'</script>';
		}
		elseif ($type=='alert')
		{
			$type= null;
			$value= '<script>alert("'.$value.'");</script>';
		}
		elseif (!$value)
		{
			$value= $type;
			$type= null;
		}

		if ($type) core::$prepend[$target][$type]= $value;
		else core::$prepend[$target][]= $value;
	}

	//=============================================================================
	function error($text,$type=E_USER_NOTICE)
	{
		static $last='';
		if (!$text) return $last;
		$last= $text;
		$backtrace= debug_backtrace();
		trigger_error("(by FW core) $text in {$backtrace[0]['file']} on line {$backtrace[0]['line']}. Triggered",$type);
	}

	//=============================================
	function halt($code,$text=null)
	{
		if ($code==301) 
		{
			header('Location: '.$text);
			$text= null;
		}
		elseif ($code==401) 
		{
			header('WWW-Authenticate: Basic realm="'.$text.'"');
			$text= null;
		}
		if (!$text)
		{
			$texts[301]= 'Moved permanently';
			$texts[400]= 'Bad request';
			$texts[401]= 'Authorisation please!';
			$texts[403]= 'Forbidden';
			$texts[404]= 'Not exist';
			$texts[410]= 'Gone';
			$texts[429]= 'Too Many Requests';
			$text= 'HTTP/1.0 '.$code.' '.$texts[$code];
		}
		header($text,true,$code);
		die($text);
	}

	//=============================================================================
	function url()
	{
		return call_user_func_array('href::url',func_get_args());
	}
	
	//=============================================================================
	function urlAdd()
	{
		return call_user_func_array('href::urlAdd',func_get_args());
	}
}