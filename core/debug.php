<?
class debug
{
	//=============================================================================
	function log()
	{
		$args= func_get_args();
		$line= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		foreach ($args as $arg)
		{
			if (is_array($arg)) $arg=jason_encode($arg);
			$line.= '; '.$arg;
  		}
		$fp= fopen('phella_log.txt','a');
  		fwrite($fp,$line."\n");
		fclose($fp);
	}
	
	//=============================================================================
	function dump($value,$level=0)
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
			$trans["\t"]='&rArr;';
			$trans["\n"]='&para;';
			$trans["\r"]='&lArr;';
			$trans["\0"]='&oplus;';
			return strtr(htmlspecialchars($value),$trans);
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
			$props= get_class_vars(get_class($value));
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

}