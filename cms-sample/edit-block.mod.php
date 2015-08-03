<?
if (!core::cms()) core::halt(403);
core::reg('run-naked',true);


if (!core::req('cms-form-action'))
{
	$no= core::req('no');
	$file= core::config('data-path')."block$no.txt";
	$block= file($file);
	core::req("headline$no", $block[0]);
	core::req("subheadline$no", $block[1]);
	unset($block[0]);
	unset($block[1]);
	core::req("text$no", implode('',$block));
	
	$f= cms::form();
	$f-> method('post') -> action(core::urlAdd('cms-form-action','save'));
	$f-> title('Edit text block '.core::req('no'));
	$f-> hidden('no') -> value($no);
	$f-> input("headline$no") -> label('Headline');
	$f-> input("subheadline$no") -> label('SubHeadline');
	$f-> textarea("text$no") -> label('Text');
	$f-> submit('Save');
	$f-> display();
}
elseif (core::req('cms-form-action')=='save')
{
	$no= core::req('no');
	$file= core::config('data-path')."block$no.txt";
	file_put_contents($file, core::req("headline$no")."\n");
	file_put_contents($file, core::req("subheadline$no")."\n",FILE_APPEND);
	file_put_contents($file, core::req("text$no"),FILE_APPEND);
	core::redirect('home');
}


return true;
