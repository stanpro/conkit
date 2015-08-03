<?
////// This sets path to the FW (traling slash is mandatory)
define('CORE','../core/');

////// Optional setup for error reporting
//error_reporting(E_ALL & ~E_STRICT);

////// Includes FW code
include(CORE.'core.php');

////// Application wide configuration
////// It override FW-wide settings in core/config.php
////// Custom configuration can be added here also
core::config('module-var','module');
core::config('default-module','home');
core::config('pre-module','head');
core::config('post-module','foot');
core::config('class-prefix','');
core::config('class-suffix','.class');

////// Launch the action
core::start();
