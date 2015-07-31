<?
core::init();

//=======================================================
function __autoload($name) 
{
	if (in_array($name,array('href','debug','sql','cms','cmsGui','cmsContext'))) include(__DIR__.'/'.$name.'.php');
	else core::library(config::get('class-sprefix').$name.config::get('class-suffix'));
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
	function init()
	{
		error_reporting(config::$items['error-reporting-lo']);

		foreach ($_COOKIE as $var=>$val) core::$request[$var]= $val;
		if (isset(config::$items['cookie-vars'])) foreach (config::$items['cookie-vars'] as $var=>$config)
		{
			core::$request[$var]= core::filter(core::req($var),$config);
		}

		foreach ($_GET as $var=>$val) core::$request[$var]= core::$requestUrl[$var]= $val;
		unset(core::$requestUrl[config::get('module-var')]);
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

		if (isset(config::$items['cookie-vars'])) foreach (config::$items['cookie-vars'] as $name=>$config) 
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
		if (isset(config::$items['required'])) foreach (config::$items['required'] as $name=>$config)
		{
			core::$request[$name]=  core::filter(core::$request[$name],$comfig);
			core::$requestUrl[$name]= core::$request[$name];
		}
		
		if (!core::module())
		{
			if (is_array(config::$items['default-module']))
			{
				$args= href::processArgs(config::$items['default-module']);
				core::$request[config::$items['module-var']]= $args['template'];
				array_merge(core::$reques,$args['req']);
			}
		}
		core::$request[config::$items['module-var']]= str_replace('..','(dot)(dot)',core::module()); //secure upper directories
		core::$request[config::$items['module-var']]= str_replace("\0",'(0)',core::module()); //secure
		core::$request[config::$items['module-var']]= str_replace('<','(lt)',core::module()); //secure
		core::$request[config::$items['module-var']]= str_replace('>','(gt)',core::module()); //secure

		// set session var
		if (core::req(session_name()) || isset(config::$items['session-vars']))
		{
			if (!session_id()) session_start();
			foreach ($_SESSION as $name=>$val) core::$request[$name]= $val;
			if (isset(config::$items['session-vars'])) foreach (config::$items['session-vars'] as $name=>$config)
			{
				core::$request[$name]= core::filter(core::req($name),$config);
				unset(core::$requestUrl[$name]);
			}
		}

		if (core::req('cms-oper')) cms::perform();
		if (core::req('core-module')=='cms-css') cmsGui::generateCss(core::req('target'));
		if (core::req('core-module')=='cms-resource') cmsGui::forward(core::req('file'));

		ob_start();
		if (config::$items['pre-models']) foreach(config::$items['pre-models'] as $model) core::insertModel($model);
		core::insert(core::module());
		$buffer= ob_get_contents();
		ob_end_clean();

		// Process pre- and post-templates
		if (!core::reg('run-naked'))
		{
			ob_start();
			if (config::$items['pre-module']) core::insert(config::$items['pre-module']);
			echo $buffer;
			if (config::$items['post-module']) core::insert(config::$items['post-module']);
			if (config::get('post-models')) foreach(config::$items['post-models'] as $model) core::insertModel($model);
			$buffer= ob_get_contents();
			ob_end_clean();
		}
		
		// Output
		echo $buffer;
		if (core::$prepend) core::error('one or more prepends were not utilized: '.implode(',',array_keys(core::$prepend)));
	}	

	//=============================================================================
	function req($key,$val=null)
	{
		if ($val!==null) core::$request[$key]= $val;
		if (isset(core::$request[$key])) return core::$request[$key];
		else return null;
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
		if (isset(core::$request[config::$items['module-var']])) return core::$request[config::$items['module-var']];
		return config::$items['default-module'];
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
		header('Location: '.href::url($args));
		exit;
	}

	//=============================================================================
	function insert($name)
	{
		$name= core::call_stack_push(func_get_args(),'module');

		// define and call model file
		$model= config::$items['model-prefix'].$name.config::$items['model-suffix'];
		if (file_exists($model))
		{
			include($model);
		}

		// define and call template file
		$template= config::$items['template-prefix'].$name.config::$items['template-suffix'];
		if ($result=file_exists($template))
		{
			include($template);
		}
		else
		{
			core::halt(404,"Module not found '$template'");
		}
 		core::call_stack_pull();
		return $result;
	}

	//=============================================================================
	function insertModel()
	{
		$model= core::call_stack_push(func_get_args(),'model');
  		$model_file= config::$items['model-prefix'].$model.config::$items['model-suffix'];
		if (file_exists($model_file))
		{
			error_reporting(config::$items['error-reporting-hi']);
			include($model_file);
			error_reporting(config::$items['error-reporting-lo']);
		}
		else core::error("Model not found '$model_file'");
		core::call_stack_pull();
	}

	//=============================================================================
	function library($model)
	{
		static $used= array();
		if (isset($used[$model])) return;
		$used[$model]= true;
		core::insertModel($model);
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
	function call_stack_push($args,$type)
	{
		$modulename= reset($args);
		core::$callStack[]=array('name'=>$modulename,'args'=>array());
		end(core::$callStack);
		$ptr= key(core::$callStack);
		unset($args[key($args)]);
		foreach ($args as $val) core::$callStack[$ptr]['args'][]=$val;
		core::$callStack[]=array('type'=>$type,'name'=>$modulename,'status'=>'open');
		return $modulename;
	}

	//=============================================================================
	function call_stack_pull()
	{
		end(core::$callStack);
		unset(core::$callStack[key(core::$callStack)]);
		end(core::$callStack);
		$ptr= key(core::$callStack);
		core::$callStack[$ptr]['status']= 'close';
	}

	//=============================================================================
	function call_stack_this($key=null,$val=null)
	{
		end(core::$callStack);
		$ptr= key(core::$callStack);
		if ($key===null) return core::$callStack[$ptr];
		if ($val===null) return core::$callStack[$ptr][$key];
		core::$callStack[$ptr][$key]=$val;
	}

	//=============================================================================
	function flatten_array($array)
	{
		$flat= array();
		foreach($array as $key=>$val)
		{
			if (is_array($val)) $flat= array_merge($flat,core::flatten_array_branch($key,$val));
			else $flat[$key]=$val;
		}
		return $flat;
	}

	//=============================================================================
	function flatten_array_branch($prefix,$array)
	{
		$flat= array();
		foreach($array as $key=>$val)
		{
			if (is_array($val)) $flat= array_merge($flat,core::flatten_array_branch($prefix.'['.$key.']',$val));
			else $flat[$prefix.'['.$key.']']=$val;
		}
		return $flat;
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
		elseif ($type=='jquery') $value= '<script type="text/javascript" src="'.config::get('jquery').'"></script>';
		elseif ($type==='cms')
		{
			core::prepend('head','jquery');
			core::prepend('head','css',config::get('cms-css'));
			core::prepend('head','js',config::get('cms-js'));
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
	function sessionVar($name,$value)
	{
		if (!session_id()) session_start();
		$_SESSION[$name]= $value;
		core::$request[$name]= $value;
	}
}