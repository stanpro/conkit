<?
if (!core::cms()) core::halt(403);
core::reg('run-naked',true);


if (!core::req('cms-form-action'))
{
	$f= cms::form();
	$f-> method('post') -> action(core::urlAdd('cms-form-action','upload'));
	$f-> title('Upload New Image');
	$f-> file('picture') -> preview('image');
	$f-> text('remark') -> value('Use only JPG for the sake of test simplicity');
	$f-> submit('Save');
	$f ->display(); 
}
elseif (core::req('cms-form-action')=='upload')
{
	$picture= core::req('picture');
	move_uploaded_file($picture['tmp_name'],core::config('data-path').'sample.jpg');
	core::req('.hide-image',false,'session');
	core::redirect('home');
}
elseif (core::req('cms-form-action')=='hide')
{
	core::req('.hide-image',true,'session');
	core::redirect('home');
}
elseif (core::req('cms-form-action')=='unhide')
{
	core::req('.hide-image',false,'session');
	core::redirect('home');
}
else
{
	core::halt(404,'Unknown action '.core::req('cms-form-action'));
}
return true;
