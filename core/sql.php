<?
class sql
{
	static $link;
	//=========================================================
	function connect($host, $user, $pass, $db=null)
	{
		self::$link= mysqli_connect($host,$user,$pass,$db);
	}
	//=========================================================
	function queryRaw($query)
	{
		$query= self::prepare($query);
		self::lastQuery($query);
		$res= mysqli_query(self::$link,$query);
		if ($res===false) core::error('MySQL Query failed');
		return $res;
	}
	//=========================================================
	function query($query,$set=null)
	{
		if ($set) $query= sql::prepareSet($query,$set);
		$res= self::queryRaw($query);
		if (substr($query,0,6)=='INSERT') $res= mysqli_insert_id(self::$link);
		if (!$res) $res= mysqli_affected_rows(self::$link);
		return $res;
	}
	//=========================================================
	function getRows($query,$keyCol=null)
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
	function getRow($query)
	{
		$res= self::queryRaw($query.' LIMIT 1', true);
		$array= mysqli_fetch_assoc($res);
		return $array;
	}
	//=========================================================
	function getValue($query)
	{
		$res= self::queryRaw($query, true);
		$array= mysqli_fetch_row(self::$link);
		return $array[0];
	}
	//=========================================================
	function prepare($query)
	{
		$query= preg_replace_callback('/(\:\:)([^:]+)(\:\:)(.+?)(\:\:)/ms',
			function ($matches) {
				$rule= $matches[2];
				$value= $matches[4];
				if (preg_match('/([0-9]+)/',$rule,$size)) $size= $size[1]; else $size= null;
				$value= core::filter($value,array('valid'=>$rule,'size'=>$size));
				if (strpos($rule,'i')===false) 
				{
					$value= mysqli_real_escape_string(sql::$link,$value);
					$value= "'".$value."'";
				}
				return $value;
			},
		$query);
		return $query;
	}
	//=========================================================
	function prepareSet($query, $set)
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
	function lastQuery($query=false)
	{
		static $last;
		if ($query) $last= $query;
		return $last;
	}
}