<?
class debug
{
	//=============================================================================
	static function log()
	{
		if (!core::config('log-file')) return;
		$args= func_get_args();
		$line= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		foreach ($args as $arg)
		{
			if (is_array($arg)) $arg=jason_encode($arg);
			$line.= '; '.$arg;
  		}
		file_put_contents(core::config('log-file'),$line."\n",FILE_APPEND);
	}
	
	//=============================================================================
	static function dump($value,$level=0)
	{
		static $objects;
		static $decor= true;
		if ($level===true)
		{
			$decor= false;
			self::dump($value);
			$decor= true;
		}
		if ($level==-1)
		{
			$trans[' ']='&blank;';
			$trans["\t"]='&map;';
			$trans["\n"]='&crarr;';
			$trans["\r"]='&larrb;';
			$trans["\0"]='&empty;';
			return '<span style="display:inline-block;max-width:500px;overflow:scroll;overflow:auto;vertical-align:top;">'.strtr(htmlspecialchars($value),$trans).'</span>';
		}
		if ($level==0)
		{
			$objects= array();
			echo '<pre style="text-align:left;">';
		}
		$type= gettype($value);
		echo $type;
		if ($type=='string')
		{
			echo '('.strlen($value).')';
			$value= self::dump($value,-1);
		}
		elseif ($type=='boolean') $value= ($value?'true':'false');
		elseif ($type=='object')
		{
			$props= get_object_vars($value);
			echo '('.count($props).') <u>'.get_class($value).'</u>';
			$o= array_search($value,$objects,true);
			if ($o===false)
			{
				$objects[]= $value;
				echo ' #'.count($objects);
				foreach($props as $key=>$val)
				{
					echo "\n".str_repeat("\t",$level+1).$key.' => ';
					self::dump($value->$key,$level+1);
				}
			}
			else echo ' => #'.($o+1);
			$value= '';
		}
		elseif ($type=='array')
		{
			echo '('.count($value).')';
			foreach($value as $key=>$val)
			{
				echo "\n".str_repeat("\t",$level+1).self::dump($key,-1).' => ';
				self::dump($val,$level+1);
			}
			$value= '';
		}
		echo " <b>$value</b>";
		if ($level==0) echo '</pre>';
	}

	//=============================================================================
	static function globals()
	{
		$ch= function($channel) 
		{
			if ($channel=='_COOKIE') $data= $_COOKIE;
			elseif ($channel=='_GET') $data= $_GET;
			elseif ($channel=='_COOKIE') $data= $_COOKIE;
			elseif ($channel=='_POST') $data= $_POST;
			elseif ($channel=='_SESSION') $data= $_SESSION;
			echo '<tr><th colspan="3">$'.$channel.'</th></tr>';
			if ($data)
			{
				foreach ($data as $name=>$value)
				{
					echo "<tr><td>$name</td><td>";
					debug::dump($value);
					echo '</td><td align="center">';
					if (!isset(core::$req[$name])) echo '&cross;';
					elseif ($channel=='_FILE') echo '&check;';
					elseif (core::$req[$name]==$value) echo '&check;';
					else echo '&ne;';
					echo '</td></tr>';
				}
			}
		};

		echo '<table border="1">';
		echo '<tr><th>Name</th><th>Value</th><th>core::req</th></tr>';
		echo $ch('_COOKIE');
		echo $ch('_GET');
		echo $ch('_POST');
		echo $ch('_FILES');
		echo $ch('_SESSION');
		echo '<tr><th colspan="3">core::req()</th></tr>';
		foreach (core::$req as $name=>$value)
		{
			echo "<tr><td>$name</td><td>";
			debug::dump($value);
			echo '</td><td></td></tr>';
		}
		echo '<tr><th colspan="3">core::reg()</th></tr>';
		foreach (core::$reg as $name=>$value)
		{
			echo "<tr><td>$name</td><td>";
			debug::dump($value);
			echo '</td><td></td></tr>';
		}
		echo '</table>';
	}	
}