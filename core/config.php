<?
core::config('error-reporting-lo', E_ALL);
core::config('error-reporting-hi', E_ALL & ~E_NOTICE & ~E_STRICT);
core::config('module-var','module');
core::config('default-module','home');
core::config('pre-module','head');
core::config('post-module','foot');
core::config('template-prefix','');
core::config('template-suffix','.tpl.php');
core::config('model-prefix','');
core::config('model-suffix','.mod.php');
core::config('class-model','-class');
//core::config('log-file','../conkit.log');
core::config('cms-realm','ConKit CMS');
core::config('jquery','https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js');
core::config('cms-fcolor','white');
core::config('cms-bcolor','red');
core::config('cms-pcolor','#d20000');
