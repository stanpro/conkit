<? core::prepend('head','title','CMS\'ed page - ConKit test') ?>

<?
	$c= cms::context() -> label('Edit') -> icon('edit') -> popup(core::url('edit-block','no',1));
	echo cms::anchor($c);
?>
<div>
	<h1><?=core::reg('headline1')?></h1>
	<h2><?=core::reg('subheadline1')?></h2>
	
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
	<?=core::reg('text1')?>
</div>

<? if(cms::admin()): ?>
	<?
		$c= cms::context() -> label('Edit') -> icon('edit') -> popup(core::url('edit-block','no',2));
		echo cms::anchor($c);
	?>
	<div>
			<h1><?=core::reg('headline2')?></h1>
			<h2><?=core::reg('subheadline2')?></h2>
			<?=core::reg('text2')?>
	</div>
<? endif ?>
