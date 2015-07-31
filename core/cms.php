<?

if (cms::admin()) core::prepend('head','cms');
		
class cms
{
	//=============================================================================
	// internal CMS perfomance
	function perform()
	{
		if (core::req('cms-oper')=='logout')
		{
			unset($_SESSION['.cms']);
			core::redirect(core::req('cms-request'));
		}
		elseif (core::req('cms-oper')=='login') cms::login();
		elseif (core::req('cms-oper')=='reset') session_destroy();
	}
	
	//=============================================================================
	function login()
	{
		core::reg('run-naked',true);
		if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER']."\n".$_SERVER['PHP_AUTH_PW']==core::req('.cms-failed')) unset($_SERVER['PHP_AUTH_USER']);
		if (!isset($_SERVER['PHP_AUTH_USER']))
		{
			$realm= config::get('cms-realm');
			if (!$realm)
			{
				$realm= strtolower($_SERVER['HTTP_HOST']);
				if (substr($realm,0,4)=='www.') $realm= substr($realm,4);
				$realm= 'phella@'.$realm;
			}
			core::halt(401,$realm);
		}
		else
		{
			$loginHandler= config::get('cms-user-handler');
			if (!$loginHandler) $loginHandler= 'cms::loginCheck';
			if ($attr=call_user_func($loginHandler,$_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']))
			{
				core::sessionVar('.cms-admin',$_SERVER['PHP_AUTH_USER']);
				core::sessionVar('.cms-attr',$attr);
				core::sessionVar('.cms-failed',false);
				if (!headers_sent()) core::halt(301,urldecode(core::req('cms-request')));
				//debug::dump(core::$request);
				//debug::dump($_SESSION);
			}
			core::sessionVar('.cms-failed',$_SERVER['PHP_AUTH_USER']."\n".$_SERVER['PHP_AUTH_PW']);
			core::halt(403,'Wrong username/password. Press "Back".');
		}
	}
	
	//=============================================================================
	function loginCheck($user,$pass)
	{
		$users= config::get('cms-users');
		if (!isset($users[$user])) return false;
		if ($users[$user]['password']===$pass)
		{
			if (isset($users[$user]['attr'])) return $users[$user]['attr'];
			return true;
		}
		return false;
	}
	
	//=============================================================================
	// outputs URL to the login page
	function loginUrl($oper='login')
	{
		return href::urlAdd('cms-oper',$oper,'cms-request',urlencode($_SERVER['REQUEST_URI']));
	}
/*	
	//=============================================================================
	function request()
	{
		if (isset(config::$items['cms-request'])) $request= config::$items['cms-request'];
		else $request= urlencode($_SERVER['REQUEST_URI']);
		return $request;
	}
*/
	//=====================================================
	function anchorGlobal($controls=false)
	{
		if (!cms::admin()) return '';
		return cmsGui::anchorGlobal($controls,cms::admin());
	}
	
	//=====================================================
	function anchor($controls,$title=null)
	{
		if (!cms::admin()) return '';
		return cmsGui::anchor($controls,$title);
	}

	//=====================================================
	function admin($arg=null)
	{
		if (!core::req('.cms-admin')) return false;
		elseif ($arg) return core::req('.cms-'.$arg);
		else return core::req('.cms-admin');
	}

}
