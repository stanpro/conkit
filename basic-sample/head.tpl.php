<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<?=core::prepend('head')?>
	</head>
	<body>
		<fieldset>
			<legend>Header (see head.tpl.php)</legend>
			<div>
				<img src="logo.png" style="width:100px; vertical-align:middle; margin-right:2em; margin-left:3em;">
				Menu: |
				<a href="<?=core::url('home')?>">Home</a> |
				<a href="<?=core::url('other')?>">Another page</a> |
				<a href="https://github.com/stanpro/conkit" target="_blank">ConKit project page</a>
			</div>
		</fieldset>
		
		<fieldset style="padding:3em;">
			<legend>Body template (see <?=core::moduleName()?>.tpl.php)</legend>
