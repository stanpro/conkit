<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<?=core::prepend('head')?>
	</head>
	<body>
		<?
			$c= cms::context();
			$c-> label('Test if  data files writable') -> icon('offline_pin') -> popup(core::url('test-files'));
			$c-> label('To ConKit project page') -> icon('cloud') -> url("https://github.com/stanpro/conkit");
			$c-> label('Logout') -> icon('lock') -> url(cms::loginUrl('logout'));
			echo cms::anchorGlobal($c);
		?>
		<fieldset>
			<legend>Header (see head.tpl.php)</legend>
			<div>
				<img src="logo.png" style="width:100px; vertical-align:middle; margin-right:2em;">
				<a href="<?=cms::loginUrl()?>">Login</a>
			</div>
		</fieldset>
		
		<fieldset style="padding:3em;">
			<legend>Body template (see <?=core::moduleName()?>.tpl.php)</legend>