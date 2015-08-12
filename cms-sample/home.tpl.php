<? core::prepend('head','title','CMS\'ed page - ConKit test') ?>

<?
	$c= cms::context() -> label('Edit') -> icon('edit') -> popup(core::url('edit-block','no',1)) -> popuptitle('Edit text block 1');
	echo cms::anchor($c,'Block 1');
?>
<div>
	<h1><?=core::vars('headline1')?></h1>
	<h2><?=core::vars('subheadline1')?></h2>
	
	<?
		$c= cms::context();
		$c-> label('Upload new image') -> icon('file_upload') -> popup(core::url('upload-image'));
		if (core::req('.hide-image')) $c-> label('Unhide image') -> icon('visibility_on') -> url(core::url('upload-image','cms-form-action','unhide'));
		else $c-> label('Hide image') -> icon('visibility_off') -> url(core::url('upload-image','cms-form-action','hide'));
		echo cms::anchor($c);
	?>
	<div style="float:right;">
		<? if (!core::req('.hide-image')): ?>
			<img src="sample.jpg" style="max-width:400px; margin-left:2em;">
		<? endif ?>
	</div>
	<?=core::vars('text1')?>
</div>

<? if(cms::admin()): ?>
	<?
		$c= cms::context() -> label('Edit') -> icon('edit') -> popup(core::url('edit-block','no',2)) -> popuptitle('Edit text block 2');
		echo cms::anchor($c,'Block 2');
	?>
	<div>
			<h1><?=core::vars('headline2')?></h1>
			<h2><?=core::vars('subheadline2')?></h2>
			<?=core::vars('text2')?>
	</div>
<? endif ?>
