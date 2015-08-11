<?
if (!core::cms()) core::halt(403);
core::reg('run-naked',true);


if (!core::req('cms-form-action'))
{
	$no= core::req('no');
	$file= core::config('data-path')."block$no.txt";
	$block= file($file);
	
	$f= cms::form();
	$f-> method('post') -> action(core::urlAdd('cms-form-action','save'));
	$f-> hidden('no') -> value($no);
	$f-> text("headline") -> label('Headline') -> value($block[0]);
	$f-> text("subheadline") -> label('SubHeadline') -> value($block[1]);
	$f-> textarea("text") -> label('Text') -> value(implode('',array_slice($block,2)));
	$f-> submit('Save');
	$f-> display();
}
elseif (core::req('cms-form-action')=='save')
{
	$no= core::req('no');
	$file= core::config('data-path')."block$no.txt";
	file_put_contents($file, core::req("headline")."\n");
	file_put_contents($file, core::req("subheadline")."\n",FILE_APPEND);
	file_put_contents($file, core::req("text"),FILE_APPEND);
	core::redirect('home');
}


return true;
