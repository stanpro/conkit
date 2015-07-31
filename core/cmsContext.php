<?

class cmsContext
{
	var $context= array();
	var $current;
	var $title;

	//==========================================
	function __construct($title=null) 
	{
		$this->title= $title;
	}

	//=====================================================
	function label($value)
	{
		if (!cms::admin()) return $this;
		$this->context[]= array();
		end($this->context);
		$this->current= key($this->context);
		$this->context[$this->current]['title']= $value;
		return $this;
	}
	
	//==========================================
	function popup($url,$title=null) 
	{
		if (!cms::admin()) return $this;
		$this->context[$this->current]['popup']= $url;
		if ($title) $this->context[$this->current]['popuptitle']= $title;
		return $this;
	}

	//==========================================
	function __call($name,$value) 
	{
		if (!cms::admin()) return $this;
		if (count($value)==1) $value= $value[0];
		$this->context[$this->current][$name]= $value;
		return $this;
	}

}