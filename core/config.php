<?
core::config('error-reporting-lo', E_ALL & ~E_STRICT);
core::config('error-reporting-hi', E_ALL & ~E_NOTICE & ~E_STRICT);
core::config('template-prefix','');
core::config('template-suffix','.tpl.php');
core::config('model-prefix','');
core::config('model-suffix','.mod.php');
//core::config('log-file','../conkit.log');
core::config('cms-realm','Phella 2 CMS Kit');
core::config('cms-css','?core-module=cms-resource&file=cms.css');
core::config('cms-js','?core-module=cms-resource&file=cms.js');
core::config('jquery','https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js');
