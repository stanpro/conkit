<?

if (cms::admin()) core::prepend('head','cms');
		
class cms
{
	//=============================================================================
	// internal CMS perfomance
	static function perform()
	{
		if (core::req('cms-oper')=='logout')
		{
			if (isset($_SESSION)) unset($_SESSION['.cms-admin']);
			if (!isset($_SESSION) || count($_SESSION)==0) setcookie(session_name(),'',0,'/'); 
			setcookie('conkit_cms_exp',core::$req['.cms-admin']['name'],0,'/'); 
			core::halt(302,urldecode(core::req('cms-request')));
		}
		elseif (core::req('cms-oper')=='login') cms::login();
	}
	
	//=============================================================================
	static function login()
	{
		core::reg('run-naked',true);
		
		$realm= core::config('cms-realm');
		if (!$realm)
		{
			$realm= strtolower($_SERVER['HTTP_HOST']);
			if (substr($realm,0,4)=='www.') $realm= substr($realm,4);
			$realm= 'ConKit@'.$realm;
		}

		if (!isset($_SERVER['PHP_AUTH_USER'])) core::halt(401,$realm);
	
		$exp= (isset($_COOKIE['conkit_cms_exp']) ? $_COOKIE['conkit_cms_exp'] : null);
		if ($_SERVER['PHP_AUTH_USER']===$exp) 
		{
			setcookie('conkit_cms_exp','',0,'/');
			core::halt(401,$realm);
		}

		$loginHandler= core::config('cms-user-check');
		if (!$loginHandler) $res= cms::loginCheck($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
		else $res= call_user_func($loginHandler,$_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
		
		if ($res!==false)
		{
			if (!$loginHandler) $res= core::reqSession('.cms-admin', array_merge(array('name'=>$_SERVER['PHP_AUTH_USER']),core::$config['cms-users'][$_SERVER['PHP_AUTH_USER']]));
			elseif (is_array($res)) $res= array_merge(array('name'=>$_SERVER['PHP_AUTH_USER'],'password'=>$_SERVER['PHP_AUTH_PW']),$res);
			else $res= array('name'=>$_SERVER['PHP_AUTH_USER'],'password'=>$_SERVER['PHP_AUTH_PW'],'attr'=>$res);
			core::reqSession('.cms-admin', $res);
			core::halt(302,urldecode(core::req('cms-request')));
		}
		else core::halt(401,$realm);
	}
	
	//=============================================================================
	static function loginCheck($user,$pass)
	{
		$users= core::config('cms-users');
		if (!isset($users[$user])) return false;
		if ($users[$user]['password']===$pass)
		{
			if (isset($users[$user]['attr'])) return $users[$user]['attr'];
			return true;
		}
		return false;
	}
	
	//=============================================================================
	static function loginUrl($oper='login')
	{
		return href::urlAdd('cms-oper',$oper,'cms-request',urlencode($_SERVER['REQUEST_URI']));
	}

	//=====================================================
	static function anchorGlobal($controls=false)
	{
		if (!cms::admin()) return '';
		return cmsGui::anchorGlobal($controls,cms::admin());
	}
	
	//=====================================================
	static function anchor($controls,$title=null)
	{
		if (!cms::admin()) return '';
		return cmsGui::anchor($controls,$title);
	}

	//=====================================================
	static function admin($arg=null)
	{
		if (!core::req('.cms-admin')) return false;
		elseif ($arg) return core::$req['.cms-admin'][$arg];
		else return core::$req['.cms-admin']['name'];
	}
	
	//=====================================================
	static function form($data=null)
	{
		return cmsGui::create('cmsGuiForm',$data);
	}

	//==========================================
	static function formData() 
	{
		$names= explode(',',core::req('cms-form-names'));
		$data= array();
		foreach ($names as $name) $data[$name]= core::req($name);
		return $data;
	}
	
	//=====================================================
	static function context()
	{
		if (!core::req('.cms-admin')) return new cmsDummy();
		return cmsGui::create('cmsContext');
	}
}

class cmsDummy extends stdClass
{
	function __call($name,$value)
	{
		return $this; 
	}
}
