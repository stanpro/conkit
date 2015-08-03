<?
if (!core::cms()) core::halt(403);
core::reg('run-naked',true);


$file= core::config('data-path').'block1.txt';
$output.= "<h2>File $file</h2>";
if (file_exists($file))
{
	if (is_writable($file))	$output.= 'is OK';
	else $output.= 'is not writable';
}
else $output.= 'does not exist';


$output.= "<hr>";


$file= core::config('data-path').'block2.txt';
$output.= "<h2>File $file</h2>";
if (file_exists($file))
{
	if (is_writable($file))	$output.= 'is OK';
	else $output.= 'is not writable';
}
else $output.= 'does not exist';


$output.= "<hr>";


$file= core::config('data-path').'sample.jpg';
$output.= "<h2>File $file</h2>";
if (file_exists($file))
{
	if (is_writable($file))	$output.= 'is OK';
	else $output.= 'is not writable';
}
else $output.= 'does not exist';


return $output;
