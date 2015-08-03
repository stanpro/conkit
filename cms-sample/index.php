<?
////// This sets path to the FW (traling slash is mandatory)
define('CORE','../core/');

////// Optional setup for error reporting
//error_reporting(E_ALL & ~E_STRICT);

////// Includes FW code
include(CORE.'core.php');

////// Application wide configuration
////// It overrides FW-wide settings in core/config.php
core::config('module-var','module');
core::config('default-module','home');
core::config('pre-module','head');
core::config('post-module','foot');
core::config('run-devel',true); //remove it
core::config('cms-users',array('admin'=>array('password'=>'demo')));
////// Custom configuration can be added here also, just don't fall into reserved keywords
core::config('data-path','./');

////// Launch the action
core::start();
