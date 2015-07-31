<?

class cmsGui
{
	//=============================================================================
	function counter($inc=0)
	{
		static $n=1; $n+=$inc;
		return $n;
	}

	//=============================================================================
	function anchor($context,$title=null)
	{
		if (is_object($context))
		{
			$title= $context->title;
			$context= $context->context;
		}
		$n= self::counter(1);
		$html= '';
		$html.= '<dfn class="cms-anchor" id="cms-anchor'.$n.'"';
		if ($title) $html.= ' title="'.$title.'"';
		$html.= '>';
		$html.= '<code>'.json_encode($context).'</code>';
		$html.= '</dfn>';
		return $html;
	}

	//=============================================================================
	function anchorGlobal($context)
	{
		if (is_object($context))
		{
			$title= cms::admin();
			$context= $context->context;
		}
		$html= '';
		$html.= '<dfn class="cms-anchor" id="cms-anchor-global"';
		if ($title) $html.= ' title="'.$title.'"';
		$html.= '>';
		$html.= '<code>'.json_encode($context).'</code>';
		$html.= '</dfn>';
		return $html;
	}

	//=============================================================================
	function forward($file)
	{
		core::reg('run-naked', true);
		if ($file!='cms.css' && $file!='cms.js') core::halt(403);
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && !config::get('run-devel'))
		{
    		header('HTTP/1.1 304 Not Modified');
 			header('Cache-Control: public, max-age=3600');
   		header('Content-Length: 0');
    		exit;
		}
		else
		{
			if ($file=='cms.css')
			{
				if (config::get('run-devel') && filemtime(CORE.'cms.php.css')>filemtime(CORE.'cms.css'))
				{
					self::generateCss('core','quiet');
				}
				$type= 'text/css';
			}
			elseif ($file=='cms.js') $type= 'text/javascript';
			header('Content-Type: '.$type);
			header('Cache-Control: public, max-age=3600');
			header('Content-Length: '.filesize(CORE.$file));
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime(CORE.$file)).' GMT');
			header('Pragma: public');
			readfile(CORE.$file);
			exit;
		}
	}

	//=============================================================================
	function generateCss($target='',$mode='')
	{
		core::reg('run-naked', true);
		ob_start();
		include(CORE.'cms.php.css');
		$buffer= ob_get_contents();
		ob_end_clean();
		if ($target=='core') $file= CORE.'cms.css';
		else $file= config::set('cms-css');
		if (file_put_contents($file,$buffer)) 
		{
			if ($mode=='quiet') return;
			exit("File $file was generated.");
		}
		else core::halt(403,"File $file is not writable");
	}
}

############################################
class cmsGuiForm
{
	var $spec;
	var $specOverall= array(
		'title'=>'',
		'method'=>'',
		'action'=>'',
		'enctype'=>'',
		'data'=>'',
		'submit'=>'',
		'validation'=>'',
	);
	var $current;
	
	//==========================================
	function __construct($data=null) 
	{
		$this->specOverall['data']= $data;
	}

	//==========================================
	function create($data=null) 
	{
		return new self($data);
	}

	//==========================================
	function __call($name,$value) 
	{
		if (count($value)==1) $value= $value[0];
		if (array_key_exists($name, $this->specOverall)) $this->specOverall[$name]= $value;
		elseif (strpos('-|input|password|select|hidden|radios|checkbox|textarea|',"|$name|")) $this->newItem($name,$value);
		else $this->spec[$this->current][$name]= $value;
		return $this;
	}

	//==========================================
	function __get($name) 
	{
		if (isset($this->specOverall[$name])) return $this->specOverall[$name]; 
		if (isset($this->spec[$this->current][$name])) return $this->spec[$this->current][$name];
		return '';
	}

	//=============================================
	function currentValue($name=null,$escaped=false)
	{
		if (is_bool($name)) {$escaped= $name; $name= null;}
		if (!$name) $name= $this->name;
		if ($this->value) $value= $this->value;
		elseif (isset(core::$request[$name])) $value= core::$request[$name];
		elseif (isset($this->specOverall['data'][$name])) $value= $this->specOverall['data'][$name];
		else $value= '';
		if ($escaped) $value= htmlspecialchars($value);
		return $value;
	}

	//==========================================
	function newItem($type,$name) 
	{
		$this->spec[]= array('type'=>$type,'name'=>$name);
		end($this->spec);
		$this->current= key($this->spec);
		return $this;
	}
	//==========================================
	function file($name) 
	{
		$this->enctype('multipart/form-data');
		return $this->newItem('file',$name);
	}
	//==========================================
	function text($name=null) 
	{
		$this->newItem('static');
		if ($name) $this->name($name);
		return $this;
	}
	//==========================================
	function separator($value=null) 
	{
		$this->newItem('separator');
		$this->value($value);
		return $this;
	}
	//==========================================
	function collapse($condition=null) 
	{
		$this->newItem('collapse');
		$this->value($condition);
		return $this;
	}
	//==========================================
	function display() 
	{
		static $uid=0;
		if (!$this->spec) return '';
		$pre= $html= $post= '';
		$names= array(); 
		foreach ($this->spec as $this->current=>$item)
		{
			$row= $this->type!='hidden' && $this->type!='collapse';
			$html.= "\n";
			if ($this->name && $this->type!='static' && !$this->omit) $names[]= $this->name;
			if ($row)
			{
				$html.= "\n";
				if ($this->type=='static' && $this->class) $addClass= ' '.$this->class;
				else $addClass= '';
				if ($this->type=='separator') $html.= '<hr>';
				else $html.= '<div class="'.$addClass.'">';
				$html.= '<label>'.$this->label.'</label>';
			}
			if ($this->type=='static') 
			{
				if (!isset($this->value)) $this->value= $this->currentValue();
				$html.= $this->value;
			}
			elseif ($this->type=='textarea')
			{
				$height= max(count(explode("\n",$this->currentValue())), ceil(strlen($this->currentValue())/40)); // 40 symbols per line
				$height= max($height,3);
				$height= min($height,10);
				$html.= '<textarea '.$this->add.' name="'.$this->name.'" rows="'.$height.'">';
				$html.= $this->currentValue();
				$html.= '</textarea>';
			}
			elseif ($this->type=='select')
			{
				$html.= '<select '.$this->add;
				$html.= ' title="value='.$this->currentValue().'"';
				if ($this->multi) $html.= ' name="'.$this->name.'[]" multiple size="7"';
				else $html.= ' name="'.$this->name.'"';
				$html.= '>';
				$html.= $this->displayOptions($this->name, $this->options);
				$html.= '</select>';
			}
			elseif ($this->type=='radios')
			{
				$html.= '<fieldset title="value='.$this->currentValue().'">';
				foreach ($this->options as $key=>$val)
				{
					$html.= '<input type="radio" name="'.$this->name.'" value="'.$key.'" id="cms-radios-'.$this->name.'-'.$key.'"';
					if ($key==$this->currentValue()) $html.= ' checked';
					$html.= ' /><label for="cms-radios-'.$this->name.'-'.$key.'">'.$val.'</label> ';
				}
				$html.= '</fieldset>';
			}
			elseif ($this->type=='checkbox')
			{
				$html.= '<fieldset title="value='.$this->currentValue().'">';
				$html.= '<input type="hidden" name="'.$this->name.'" value="'.$this->currentValue().'">';
				$html.= '<input type="checkbox" '.$this->add.' onchange="cms_checkbox_adapt(this,\''.$this->check.'\',\''.$this->uncheck.'\')"';
				if ($this->check==$this->currentValue()) $html.= ' checked';
				$html.= ' />&nbsp;';
				$html.= '</fieldset>';
			}
			elseif ($this->type=='separator')
			{
				$html.= $this->value;
			}
			elseif ($this->type=='collapse') {}
			else 
			{
				$html.= '<input type="'.$this->type.'" id="'.$this->name.'"'.$this->add;
				$html.= ' name="'.$this->name.'" value="'.$this->currentValue(true).'" '.$this->html;
				if ($this->class) $html.= ' class="'.$this->class.'"';
				$html.= '>';
			}
			if ($row)
			{
				$html.= '</div>';
				if ($this->span) $span= "rowspan=\"{$this->span}\"";
				else $span= '';
			}
		}
		if ($names)
		{
			$html.= '<input type="hidden" name="cms-form-names" value="'.implode(',',$names).'" />';
			list($submitButton,$submitValue)= explode('|',$this->submit);
			if (!$submitValue) $submitValue= $submitButton;
			$html.= '<input type="hidden" name="cms-form-oper" value="'.$submitValue.'">';
			$html.= '<h4><input type="submit" value="'.$submitButton.'" /></h4>';
			$add= '';
			if ($this->validation) $add.= ' onSubmit="'.$this->validation.'"';
			if ($this->enctype) $add.= ' enctype="'.$this->enctype.'"';
			$pre.= '<form method="'.$this->method.'" action="'.$this->action.'" '.$add.'>';
			//$pre.= '<div class="error">'.'</div>';
			$post.= '</form>';
		}
		else
		{
			$pre.= '<div>';
			$post.= '</div>';
		}
		echo $pre.$html.$post.$this->cargo;
	}

	//==========================================
	function displayOptions($name,$list) 
	{
		reset($list); $keys= !(key($list)===0);
		$html= '';
		foreach($list as $value=>$text)
		{
			if (!$keys) $value= $text;
			elseif (!$text) $text= $value;
			if ($value==$this->currentValue($name)) $selected='selected'; else $selected='';
			$html.= "<option value=\"$value\" $selected>$text</option>";
		}
		return $html;
	}

	//==========================================
	function submittedData() 
	{
		$names= explode(',',core::req('cms-form-names'));
		$data= array();
		foreach ($names as $name) $data[$name]= core::req($name);
		return $data;
	}
}