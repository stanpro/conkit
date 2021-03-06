<?
class sql
{
	static $link;
	static $data;
	//=========================================================
	static function connect($host, $user, $pass, $db=null)
	{
		self::$link= mysqli_connect($host,$user,$pass,$db);
		return (boolean)self::$link;
	}
	//=========================================================
	static function queryRaw($query)
	{
		if (sql::$data) $query= self::prepare($query);
		self::lastQuery($query);
		$res= mysqli_query(self::$link,$query);
		if ($res===false) core::error('MySQL Query failed');
		return $res;
	}
	//=========================================================
	static function query($query)
	{
		$res= self::queryRaw($query);
		if (substr($query,0,6)=='INSERT') $res= mysqli_insert_id(self::$link);
		if (!$res) $res= mysqli_affected_rows(self::$link);
		return $res;
	}
	//=========================================================
	static function getRows($query,$keyCol=null)
	{
		$array= array();
		$res= self::queryRaw($query);
		if (mysqli_num_rows($res)>0)
		{			
			if ($keyCol)
			{
				if (!is_array($keyCol)) $keyCol= array($keyCol);
				if (count($keyCol)==1) while ($item= mysqli_fetch_assoc($res)) $array[$item[$keyCol[0]]]= $item;
				elseif (count($keyCol)==2) while ($item= mysqli_fetch_assoc($res)) $array[$item[$keyCol[0]]][$item[$keyCol[2]]]= $item;
			}
			else while ($item= mysqli_fetch_assoc($res)) $array[]= $item;
		}
		mysqli_free_result($res);
		return $array;
	}
	//=========================================================
	static function getRow($query)
	{
		$res= self::queryRaw($query.' LIMIT 1', true);
		$array= mysqli_fetch_assoc($res);
		return $array;
	}
	//=========================================================
	static function getValue($query)
	{
		$res= self::queryRaw($query, true);
		$array= mysqli_fetch_row(self::$link);
		return $array[0];
	}
	//=========================================================
	static function data()
	{
		self::$data= func_get_args();
		if (is_array(self::$data[0]) && key(self::$data[0])===0) self::$data= self::$data[0];
	}
	//=========================================================
	static function prepare($query)
	{
		$query= preg_replace_callback('/(\?)([a-z0-9@]*)/ms',
			function ($matches) 
			{
				static $i= 0;
				$rule= $matches[2];
				if (!isset(sql::$data[$i])) return core::error('Undefined data element #'.$i);
				$value= sql::$data[$i];
				if (is_array($value))
				{
				   $list= '';
				   foreach ($value as $col=>$val)
				   {
						$val= mysqli_real_escape_string(sql::$link,$val);
						$list.= ",`$col`='$val'";
				   }
				   $list= substr($list,1);  // remove very first comma
				   $value= $list;
				}
				else
				{
					if (strpos($rule,'i')!==false) $value= (int)$value;
					elseif (strpos($rule,'d')!==false) $value= "'".date("Y-m-d H:i:s", $value)."'";
					else				 
					{
						if (preg_match('/([0-9]+)/',$rule,$size)) $size= $size[1]; else $size= null;
						$value= core::filter($value,array('valid'=>$rule,'size'=>$size));
						$value= mysqli_real_escape_string(sql::$link,$value);
						$value= "'".$value."'";
					}
				}
				$i++;
				return $value;
			},
		$query);
		sql::$data= null;
		return $query;
	}
	//=========================================================
	static function prepareSet($query, $set)
	{
	   $set_text= '';
	   foreach ($set as $col=>$value)
	   {
	   		$value= mysqli_real_escape_string(sql::$link,$value);
	   		$set_text.= ",`$col`='$value'";
	   }
	   $set_text= substr($set_text,1);  // remove very first comma
	   return str_replace('::=::',$set_text,$query);
	}
	//=========================================================
	static function lastQuery($query=false)
	{
		static $last;
		if ($query) $last= $query;
		return $last;
	}
}